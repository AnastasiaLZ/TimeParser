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
use Exception;

class TimeParser
{
    /**
     * Current version number of TimeParser.
     */
    const VERSION = '3.0.0-DEV';

    /**
     * @var array
     */
    protected $languages;
    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @param mixed $languages
     */
    public function __construct($languages = null)
    {
        if (is_string($languages)) {
            $languages = [$languages];
        }

        if (is_array($languages)) {
            $rules     = $this->getAvailableRules();
            $classes   = $this->getAvailableLanguages();
            $available = array_merge($rules, $classes);
            $languages = array_map('mb_strtolower', array_filter($languages, 'is_string'));

            if ($languages !== ['all']) {
                $available = array_intersect($languages, $available);
                $unknown   = array_diff($languages, $available);

                if (empty($available) || $unknown) {
                    throw new Exception(sprintf(
                        'Unknown language used: %s',
                        implode(', ', $unknown)
                    ));
                }
            }

            foreach ($available as $name) {
                if (in_array($name, $classes)) {
                    $class = 'wapmorgan\\TimeParser\\Language\\'.ucfirst($name);
                    $class = new $class();

                    $this->addLanguage($class);
                } else {
                    $this->addLanguage(Language::createFromName($name));
                }
            }
        }
    }

    /**
     * Enables or disables debugging messages.
     *
     * @param bool $debug
     */
    public function setDebug($debug = false)
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    /**
     * Parses the string to receive a DateTime object from it.
     *
     * @param string $string              The input string
     * @param bool   $falseWhenNotChanged Return false if parsing had no effect
     * @param string &$result
     *
     * @return bool|DateTimeImmutable
     */
    public function parse($string, $falseWhenNotChanged = false, &$result = null)
    {
        if (empty($this->languages) || !is_array($this->languages)) {
            throw new Exception('You must add at least one language.');
        }

        $datetime = $current = new DateTimeImmutable();
        $matches  = null;

        foreach ($this->languages as $language) {
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
                        while ($this->match($pattern, $string, $matches)) {
                            $datetime = call_user_func([$language, $method], $rule, $matches, $datetime);
                        }
                    }
                }
            }
        }

        $result = $this->stripWhitespace($string);

        if ($datetime === $current && $falseWhenNotChanged) {
            return false;
        }

        return $datetime;
    }

    /**
     * Adds a language.
     *
     * @param Interfaces\LanguageInterface $language
     */
    public function addLanguage(Interfaces\LanguageInterface $language)
    {
        $name = mb_strtolower($language->getName());

        $language->setTimeParser($this);

        $this->languages[$name] = $language;

        return $this;
    }

    /**
     * Get the available languages rules from rules folder.
     *
     * @return array The available rules
     */
    public function getAvailableRules()
    {
        return array_map(function ($lang) {
            return strtolower(basename($lang, '.json'));
        }, glob(__DIR__.'/../rules/*.json'));
    }

    /**
     * Get the available languages classes from Language folder.
     *
     * @return array The available languages classes
     */
    public function getAvailableLanguages()
    {
        return array_map(function ($lang) {
            return strtolower(basename($lang, '.php'));
        }, glob(dirname(__FILE__).'/Language/*.php'));
    }

    /**
     * Prints the debug message.
     *
     * @param string $message
     */
    public function debug($message)
    {
        static $isCli;

        if (!$this->debug) {
            return;
        }

        if (null === $isCli) {
            $isCli = defined('STDIN');
        }

        $message = htmlspecialchars($message).PHP_EOL;

        if ($isCli) {
            $message = nl2br($message);
        }

        echo $message;
    }

    /**
     * Strips whitespace from the beginning and end of a string and replaces repeated spaces with one.
     *
     * @param string $string The input string
     *
     * @return string The stripped string
     */
    public function stripWhitespace($string)
    {
        return preg_replace(['/^[\pZ\pC]+|[\pZ\pC]+$/u', '/[\pZ\pC]{1,}/u'], ['', ' '], $string);
    }

    /**
     * Searches string for a match to the regular expression given in pattern.
     *
     * @param string $pattern  The pattern to search for, as a string
     * @param string &$string  The input string
     * @param array  &$matches The matches fills with the results of search
     *
     * @return bool
     */
    public function match($pattern, &$string, &$matches)
    {
        if (preg_match($pattern, $string, $matches, PREG_OFFSET_CAPTURE)) {
            $string = substr($string, 0, $matches[0][1]).substr($string, $matches[0][1] + strlen($matches[0][0]));
            $string = $this->stripWhitespace($string);

            $this->debug('Matched: '.$pattern);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function prepareString($string)
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
}
