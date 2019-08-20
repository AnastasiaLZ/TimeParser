<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser\Traits;

trait LanguageTranslatorTrait
{
    /**
     * @var array
     */
    protected $validPronouns = ['last', 'this', 'next'];

    /**
     * @var array
     */
    protected $monthToNumber = [
        'january'   => 1,
        'february'  => 2,
        'march'     => 3,
        'april'     => 4,
        'may'       => 5,
        'june'      => 6,
        'july'      => 7,
        'august'    => 8,
        'september' => 9,
        'october'   => 10,
        'november'  => 11,
        'december'  => 12,
    ];

    /**
     * @param string $string
     * @return int
     */
    public function translateMonth(string $string): int
    {
        $month = isset($this->months[$string])
            ? $this->months[$string]
            : $string;

        return isset($this->monthToNumber[$month])
            ? $this->monthToNumber[$month]
            : (int) $month;
    }

    /**
     * @param string $string
     * @param string $default
     * @return string
     */
    public function translatePronoun(string $string, string $default = 'this'): string
    {
        $pronoun = isset($this->pronouns[$string])
            ? $this->pronouns[$string]
            : $default;

        return in_array($pronoun, $this->validPronouns, true) ? $pronoun : $default;
    }

    public function translateUnit(string $string): string
    {
        return isset($this->units[$string])
            ? $this->units[$string]
            : $string;
    }

    public function translateWeekDay(string $string): string
    {
        return isset($this->weekDays[$string])
            ? $this->weekDays[$string]
            : $string;
    }

    public function translateTimeShift(string $string): string
    {
        return isset($this->timeshift[$string])
            ? $this->timeshift[$string]
            : $string;
    }
}
