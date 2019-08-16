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
     * {@inheritdoc}
     */
    public function translateMonth($string)
    {
        $month = isset($this->months[$string])
            ? $this->months[$string]
            : $string;

        return isset($this->monthToNumber[$month])
            ? $this->monthToNumber[$month]
            : (int) $month;
    }

    /**
     * {@inheritdoc}
     */
    public function translatePronoun($string)
    {
        $pronoun = isset($this->pronouns[$string])
            ? $this->pronouns[$string]
            : 'this';

        return in_array($pronoun, $this->validPronouns, true) ? $pronoun : 'this';
    }

    /**
     * {@inheritdoc}
     */
    public function translateUnit($string, $default = null)
    {
        return isset($this->units[$string])
            ? $this->units[$string]
            : (null === $default ? $string : $default);
    }

    /**
     * {@inheritdoc}
     */
    public function translateWeekDay($string, $default = null)
    {
        return isset($this->weekDays[$string])
            ? $this->weekDays[$string]
            : (null === $default ? $string : $default);
    }

    /**
     * {@inheritdoc}
     */
    public function translateTimeShift($string, $default = null)
    {
        return isset($this->timeshift[$string])
            ? $this->timeshift[$string]
            : (null === $default ? $string : $default);
    }
}
