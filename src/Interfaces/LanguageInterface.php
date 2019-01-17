<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser\Interfaces;

use wapmorgan\TimeParser\TimeParser;

interface LanguageInterface
{
    /**
     * Get language name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get list of absolute rules.
     *
     * @return array
     */
    public function getAbsoluteRules();

    /**
     * Get listof absolute rules.
     *
     * @return array
     */
    public function getRelativeRules();

    /**
     * Get both absolute and relative rules.
     *
     * @return array
     */
    public function getRules();

    /**
     * Get list of months.
     *
     * @return array
     */
    public function getMonths();

    /**
     * Get list of pronouns.
     *
     * @return array
     */
    public function getPronouns();

    /**
     * Get list of units.
     *
     * @return array
     */
    public function getUnits();

    /**
     * Get list of days of week.
     *
     * @return array
     */
    public function getWeekDays();

    /**
     * @param string $string
     *
     * @return int
     */
    public function translateMonth($string);

    /**
     * @param string $string
     * @param mixed  $default
     *
     * @return string
     */
    public function translatePronoun($string);

    /**
     * @param string $string
     * @param mixed  $default
     *
     * @return string
     */
    public function translateUnit($string);

    /**
     * @param string $string
     * @param mixed  $default
     *
     * @return string
     */
    public function translateWeekDay($string);

    /**
     * @param TimeParser $timeParser
     */
    public function setTimeParser(TimeParser $timeParser);

    /**
     * Try to translate words to number.
     *
     * @param string $alpha
     *
     * @return int|bool
     */
    public function wordsToNumber($string);

    /**
     * Parses absolute rules.
     *
     * @param string             $rule
     * @param array              $matches
     * @param \DateTimeImmutable $datetime]
     *
     * @return \DateTimeImmutable
     */
    public function parseAbsolute($rule, array $matches, $datetime);

    /**
     * Parses relative rules.
     *
     * @param string             $rule
     * @param array              $matches
     * @param \DateTimeImmutable $datetime
     *
     * @return \DateTimeImmutable
     */
    public function parseRelative($rule, array $matches, $datetime);
}
