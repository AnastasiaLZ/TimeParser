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
use wapmorgan\TimeParser\Traits\LanguageGetterTrait;
use wapmorgan\TimeParser\Traits\LanguageTranslatorTrait;

class Language implements Interfaces\LanguageInterface
{
    use LanguageGetterTrait;
    use LanguageTranslatorTrait;

    /**
     * Regex to match valid hours.
     */
    const HOURS = '(0?[1-9]|1[0-9]|2[0-3])';

    /**
     * Regex to match valid minutes and seconds.
     */
    const MINUTES = '([0-9]|[0-5][0-9])';

    /**
     * Regex to match valid language name.
     */
    const NAME_REGEX = '/^[a-z]+(\s[a-z]+)?$/i';

    /**
     * Language name.
     *
     * @var string
     */
    protected $name;

    /**
     * Absolute rules.
     *
     * @var array
     */
    protected $absoluteRules;

    /**
     * Relative rules.
     *
     * @var array
     */
    protected $relativeRules;

    /**
     * List of months.
     *
     * @var array
     */
    protected $months;

    /**
     * List of pronouns.
     *
     * @var array
     */
    protected $pronouns;

    /**
     * List of units.
     *
     * @var array
     */
    protected $units;

    /**
     * List of days of week.
     *
     * @var array
     */
    protected $weekDays;

    /**
     * @var array
     */
    protected $timeshift;

    /**
     * Advanced rules.
     *
     * @var array
     */
    protected $advanced;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     * @param string          $name      Language name
     * @param array           $absolute  Absolute rules
     * @param array           $relative  Relative rules
     * @param array           $months    List of months
     * @param array           $pronouns  List of pronouns
     * @param array           $units     List of units
     * @param array           $weekDays  List of days of week
     * @param array           $timeshift List of days of week
     */
    public function __construct(
        LoggerInterface $logger,
        string $name,
        array $absolute,
        array $relative,
        array $months,
        array $pronouns,
        array $units,
        array $weekDays,
        array $timeshift
    ) {
        foreach ($relative as $rule => $regex) {
            if (strpos($rule, '@') === 0 && strpos($rule, '_') !== false) {
                $this->advanced[substr($rule, 1)] = $regex;

                unset($relative[$rule]);
            }
        }

        $this->logger        = $logger;
        $this->name          = $name;
        $this->absoluteRules = $absolute;
        $this->relativeRules = $relative;
        $this->months        = $months;
        $this->pronouns      = $pronouns;
        $this->units         = $units;
        $this->weekDays      = $weekDays;
        $this->timeshift     = $timeshift;
    }

    /**
     * Creates a Language instance from array.
     *
     * @param LoggerInterface $logger
     * @param array           $data
     *
     * @return Language
     * @throws TimeParserException
     */
    public static function createFromArray(LoggerInterface $logger, array $data): Language
    {
        $data = static::validateData($data);

        return new static(
            $logger,
            $data['name'],
            $data['absolute'],
            $data['relative'],
            $data['months'],
            $data['pronouns'],
            $data['units'],
            $data['week_days'],
            $data['timeshift']
        );
    }

    /**
     * Creates a Language instance from language name.
     *
     * @param LoggerInterface $logger
     * @param string          $name
     *
     * @return Language
     * @throws TimeParserException
     */
    public static function createFromName(LoggerInterface $logger, string $name = null): Language
    {
        $data = static::findRuleByName($name);

        return static::createFromArray($logger, $data);
    }

    public function wordsToNumber(string $alpha): ?int
    {
        $alpha = strtr($alpha, $this->units);
        $parts = array_filter(array_map(
            function ($val) {
                return floatval($val);
            },
            preg_split('/[\s-]+/', $alpha)
        ));

        return $parts ? array_sum($parts) : null;
    }

    public function parseAbsolute(string $rule, array $matches, DateTimeImmutable $datetime): DateTimeImmutable
    {
        $year      = isset($matches['year'][0]) ? $matches['year'][0] : $datetime->format('Y');
        $month     = isset($matches['month'][0]) ? $matches['month'][0] : $datetime->format('m');
        $day       = isset($matches['day'][0]) ? (int) $matches['day'][0] : $datetime->format('d');
        $hour      = isset($matches['hour'][0]) ? (int) $matches['hour'][0] : '';
        $min       = isset($matches['min'][0]) ? (int) $matches['min'][0] : '';
        $sec       = isset($matches['sec'][0]) ? (int) $matches['sec'][0] : '';
        $digit     = isset($matches['digit'][0]) ? $matches['digit'][0] : '';
        $alpha     = isset($matches['alpha'][0]) ? $matches['alpha'][0] : '';
        $pronoun   = isset($matches['pronoun'][0]) ? $matches['pronoun'][0] : '';
        $weekday   = isset($matches['weekday'][0]) ? $matches['weekday'][0] : '';
        $timeshift = isset($matches['timeshift'][0]) ? $matches['timeshift'][0] : '';

        $month = $this->translateMonth($month);

        if ($digit === '' && $alpha === '') {
            $digit = 1;
        } elseif ($alpha !== '' && strpos($rule, 'digit') !== false) {
            $digit = $this->translateUnit($alpha);

            if (!is_numeric($digit)) {
                $digit = $this->wordsToNumber($digit);
            }
        }

        $replace = [
            '$year'      => (int) $year,
            '$month'     => sprintf('%02d', max(1, min(12, $month))),
            '$day'       => sprintf('%02d', max(1, min(31, $day))),
            '$hour'      => sprintf('%02d', max(0, min(23, $hour))),
            '$min'       => sprintf('%02d', max(0, min(59, $min))),
            '$sec'       => sprintf('%02d', max(0, min(59, $sec))),
            '$digit'     => is_numeric($digit) ? sprintf('%02d', $digit) : '',
            '$alpha'     => $alpha,
            '$pronoun'   => $this->translatePronoun($pronoun),
            '$weekday'   => $this->translateWeekDay($weekday),
            '$timeshift' => $this->translateTimeShift($timeshift),
        ];

        $date = strtr($rule, $replace);

        if (false !== $time = strtotime($date)) {
            if ($date === 'next week') {
                $date = '+1 week';
            } elseif ($date === 'last week') {
                $date = '-1 week';
            }

            $datetime = $datetime->modify($date);

            if (strpos($date, ':') === false && $hour && $min) {
                $datetime = $datetime->setTime($hour, $min, $sec);
            }

            $this->logger->debug(sprintf('Set datetime: %s (%s)', $datetime->format('r'), $date));
        }

        return $datetime;
    }

    public function parseRelative(string $rule, array $matches, DateTimeImmutable $datetime): DateTimeImmutable
    {
        if ($rule === '@relative'
            && isset($matches['relative'][0])
            && isset($this->advanced['relative_split'])
            && isset($this->advanced['relative_join'])
        ) {
            $this->logger->debug('Advanced relative parsing');

            $relative = $matches['relative'][0];
            $split    = $this->advanced['relative_split'];
            $join     = $this->advanced['relative_join'];
            $phrases  = array_filter(array_map('trim', preg_split($split, $relative)));
            $string   = sprintf('%s %s', $join, implode(" {$join} ", $phrases));

            foreach ($this->getRelativeRules() as $rule => $regex) {
                if (strpos($rule, '+$1') !== 0) {
                    continue;
                }

                if ($this->match($regex, $string, $matches)) {
                    $datetime = $this->parseRelative($rule, $matches, $datetime);
                }
            }

            return $datetime;
        }

        $digit = isset($matches['digit'][0]) ? $matches['digit'][0] : '';
        $alpha = isset($matches['alpha'][0]) ? $matches['alpha'][0] : '';

        if ($digit === '' && $alpha === '') {
            $digit = 1;
        }

        if ($alpha !== '') {
            $digit = $this->translateUnit($alpha);

            if (!is_numeric($digit)) {
                $digit = $this->wordsToNumber($alpha);
            }
        }

        if ($digit && is_numeric($digit)) {
            if (preg_match('/^[a-z]+$/', $rule)) {
                $modify = "+{$digit} {$rule}";
            } else {
                $modify = str_replace('$1', $digit, $rule);
            }

            if (preg_match('/^[\+\-]\d+ [a-z]+$/', $modify)) {
                $this->logger->debug('Add offset: '.$modify);
                $datetime = $datetime->modify($modify);
            }
        }

        return $datetime;
    }

    /**
     * Try to validate input data array.
     *
     * @param array $data
     *
     * @return array
     * @throws TimeParserException
     */
    protected static function validateData(array $data): array
    {
        if (!isset($data['language']) || !is_string($data['language']) || !preg_match(static::NAME_REGEX,
                $data['language'])) {
            throw new TimeParserException('Invalid language name');
        }

        if (!isset($data['rules']['absolute']) || !is_array($data['rules']['absolute'])) {
            throw new TimeParserException('"rules.absolute" must be an array');
        }

        if (!isset($data['rules']['relative']) || !is_array($data['rules']['relative'])) {
            throw new TimeParserException('"rules.relative" must be an array');
        }

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new TimeParserException('"months" must be an array');
        }

        if (!isset($data['pronouns']) || !is_array($data['pronouns'])) {
            throw new TimeParserException('"pronouns" must be an array');
        }

        if (!isset($data['units']) || !is_array($data['units'])) {
            throw new TimeParserException('"months" must be an array');
        }

        if (!isset($data['week_days']) || !is_array($data['week_days'])) {
            throw new TimeParserException('"week_days" must be an array');
        }

        if (!isset($data['timeshift']) || !is_array($data['timeshift'])) {
            throw new TimeParserException('"timeshift" must be an array');
        }

        return [
            'name'      => $data['language'],
            'absolute'  => $data['rules']['absolute'],
            'relative'  => $data['rules']['relative'],
            'months'    => $data['months'],
            'pronouns'  => $data['pronouns'],
            'units'     => $data['units'],
            'week_days' => $data['week_days'],
            'timeshift' => $data['timeshift'],
        ];
    }

    /**
     * Finds rule by language name.
     *
     * @param string $name
     * @param bool   $validate
     *
     * @return array
     * @throws TimeParserException
     */
    protected static function findRuleByName(string $name, bool $validate = false): array
    {
        if (empty($name) || !is_string($name) || !preg_match(static::NAME_REGEX, $name)) {
            throw new TimeParserException('Invalid language name');
        }

        $name = mb_strtolower($name);
        $file = dirname(__DIR__).DIRECTORY_SEPARATOR.'rules'.DIRECTORY_SEPARATOR.$name.'.json';

        if (!file_exists($file)) {
            throw new TimeParserException(sprintf('Couldn\'t find language file "%s"', $name));
        }

        $data = json_decode(file_get_contents($file), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TimeParserException(json_last_error_msg());
        }

        return $validate ? static::validateData($data) : $data;
    }

    /**
     * Searches string for a match to the regular expression given in pattern.
     *
     * @param string  $pattern The pattern to search for, as a string
     * @param string &$string  The input string
     * @param array  &$matches The matches fills with the results of search
     *
     * @return bool
     */
    public function match(string $pattern, string &$string, ?array &$matches): bool
    {
        if (preg_match($pattern, $string, $matches, PREG_OFFSET_CAPTURE)) {
            $string = substr($string, 0, $matches[0][1]).substr($string, $matches[0][1] + strlen($matches[0][0]));
            $string = $this->stripWhitespace($string);

            $this->logger->debug('Matched: '.$pattern);

            return true;
        }

        return false;
    }

    /**
     * Strips whitespace from the beginning and end of a string and replaces repeated spaces with one.
     *
     * @param string $string The input string
     *
     * @return string The stripped string
     */
    private function stripWhitespace(string $string): string
    {
        return preg_replace(['/^[\pZ\pC]+|[\pZ\pC]+$/u', '/[\pZ\pC]{1,}/u'], ['', ' '], $string);
    }
}
