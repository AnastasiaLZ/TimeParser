<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use wapmorgan\TimeParser\Exceptions\TimeParserException;

class TimeParser
{
    /**
     * Current version number of TimeParser.
     */
    const VERSION = '3.0.0-DEV';

    /**
     * @var Language[]
     */
    protected $languages;

    /**
     * @var Language
     */
    protected $commonRules;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     * @param string[]        $languages
     *
     * @throws TimeParserException
     */
    public function __construct(LoggerInterface $logger, array $languages = null)
    {
        $this->commonRules = Language::createFromName($logger, 'common');

        $this->logger = $logger;

        $rules     = static::getAvailableRules();
        $classes   = static::getAvailableLanguages();
        $available = array_merge($rules, $classes);

        if (!is_null($languages)) {
            $languages = array_map('mb_strtolower', array_filter($languages, 'is_string'));
            $available = array_intersect($languages, $available);
            $unknown   = array_diff($languages, $available);

            if (empty($available) || $unknown) {
                throw new TimeParserException(sprintf(
                    'Unknown language used: %s',
                    implode(', ', $unknown)
                ));
            }
        }

        foreach ($available as $name) {
            if (in_array($name, $classes)) {
                $class = 'wapmorgan\\TimeParser\\Language\\'.ucfirst($name);
                $class = new $class($logger);

                $this->addLanguage($class);
            } else {
                $this->addLanguage(Language::createFromName($logger, $name));
            }
        }
    }

    /**
     * Parses the string to receive a DateTime object from it.
     *
     * @param string                 $string
     * @param DateTimeImmutable|null $initialDate
     *
     * @return DateTimeImmutable|null
     *
     * @throws TimeParserException
     */
    public function parse(string $string, ?DateTimeImmutable $initialDate = null): ?ParsedDateTime
    {
        if (empty($this->languages) || !is_array($this->languages)) {
            throw new TimeParserException('You must add at least one language.');
        }

        $datetime      = !is_null($initialDate) ? $initialDate : new DateTimeImmutable();
        $matches       = null;
        $text          = $this->prepareString($string);
        $matchesResult = [];

        $language = $this->detectLang($text);

        if ($language) {
            foreach ($language->getRules() as $type => $rules) {
                $method = 'parse'.ucfirst($type);

                if (!method_exists($language, $method)) {
                    continue;
                }

                foreach ($rules as $rule => $patterns) {
                    if (!is_array($patterns)) {
                        $patterns = [$patterns];
                    }

                    foreach ($patterns as $pattern) {
                        while ($language->match($pattern, $text, $matches)) {
                            $matchesResult[$pattern] = $matches;
                            $datetime                = call_user_func([$language, $method], $rule, $matches, $datetime);
                        }
                    }
                }
            }
        }

        foreach ($this->commonRules->getRules() as $type => $rules) {
            $method = 'parse'.ucfirst($type);

            if (!method_exists($this->commonRules, $method)) {
                continue;
            }

            foreach ($rules as $rule => $patterns) {
                if (!is_array($patterns)) {
                    $patterns = [$patterns];
                }

                foreach ($patterns as $pattern) {
                    while ($this->commonRules->match($pattern, $text, $matches)) {
                        $matchesResult[$pattern] = $matches;
                        $datetime                = call_user_func([$this->commonRules, $method], $rule, $matches, $datetime);
                    }
                }
            }
        }

        if (empty($matchesResult)) {
            return null;
        }

        $langName = $language ? $language->getName() : $this->commonRules->getName();

        $result = new ParsedDateTime($datetime, $matchesResult, $text, $langName);

        return $result;
    }

    /**
     * Adds a language.
     *
     * @param Interfaces\LanguageInterface $language
     *
     * @return TimeParser
     */
    public function addLanguage(Interfaces\LanguageInterface $language): TimeParser
    {
        $name = mb_strtolower($language->getName());

        $this->languages[$name] = $language;

        return $this;
    }

    /**
     * Get the available languages rules from rules folder.
     *
     * @return array The available rules
     */
    public static function getAvailableRules(): array
    {
        return array_map(function ($lang) {
            return strtolower(basename($lang, '.json'));
        }, glob(__DIR__.'/../rules/[^common].json'));
    }

    /**
     * Get the available languages classes from Language folder.
     *
     * @return array The available languages classes
     */
    public static function getAvailableLanguages(): array
    {
        return array_map(function ($lang) {
            return strtolower(basename($lang, '.php'));
        }, glob(dirname(__FILE__).'/Language/*.php'));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function prepareString(string $string): string
    {
        if (function_exists('mb_strtolower')) {
            if (($encoding = mb_detect_encoding($string)) != 'UTF-8') {
                $string = mb_convert_encoding($string, 'UTF-8', $encoding);
            }

            $string = mb_strtolower($string);
        } else {
            $string = strtolower($string);
        }

        return $this->stripWhitespace($string);
    }

    /**
     * Strips whitespace from the beginning and end of a string and replaces repeated spaces with one.
     *
     * @param string $string The input string
     *
     * @return string The stripped string
     */
    public function stripWhitespace(string $string): string
    {
        return preg_replace(['/^[\pZ\pC]+|[\pZ\pC]+$/u', '/[\pZ\pC]{1,}/u'], ['', ' '], $string);
    }

    /**
     * @param $text
     *
     * @return Language|null
     */
    private function detectLang(string $text): ?Language
    {
        $matchesCount = [];

        /** @var Language $language */
        foreach ($this->languages as $key => $language) {
            $string             = $text;
            $matchesCount[$key] = 0;
            foreach ($language->getRules() as $type => $rules) {
                $method = 'parse'.ucfirst($type);

                if (!method_exists($language, $method)) {
                    continue;
                }

                foreach ($rules as $rule => $patterns) {
                    if (!is_array($patterns)) {
                        $patterns = [$patterns];
                    }

                    foreach ($patterns as $pattern) {
                        while ($language->match($pattern, $string, $matches)) {
                            $matchesCount[$key] += count(array_filter($matches, function ($match) {
                                return $match[0] !== '';
                            }));
                        }
                    }
                }
            }
        }

        if (!$maxMatches = max($matchesCount)) {
            return null;
        }

        $langName = array_search($maxMatches, $matchesCount);

        return $this->languages[$langName];
    }
}
