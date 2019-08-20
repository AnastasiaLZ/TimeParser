<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser\Interfaces;

use DateTimeImmutable;
use wapmorgan\TimeParser\TimeParser;

interface LanguageInterface
{
    /**
     * Get language name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get list of absolute rules.
     *
     * @return array
     */
    public function getAbsoluteRules(): array;

    /**
     * Get listof absolute rules.
     *
     * @return array
     */
    public function getRelativeRules(): array;

    /**
     * Get both absolute and relative rules.
     *
     * @return array
     */
    public function getRules(): array;

    /**
     * Get list of months.
     *
     * @return array
     */
    public function getMonths(): array;

    /**
     * Get list of pronouns.
     *
     * @return array
     */
    public function getPronouns(): array;

    /**
     * Get list of units.
     *
     * @return array
     */
    public function getUnits(): array;

    /**
     * Get list of days of week.
     *
     * @return array
     */
    public function getWeekDays(): array;

    /**
     * @param string $string
     *
     * @return int
     */
    public function translateMonth(string $string): int;

    /**
     * @param string $string
     * @param string $default
     *
     * @return string
     */
    public function translatePronoun(string $string, string $default = 'this'): string;

    /**
     * @param string $string
     *
     * @return string
     */
    public function translateUnit(string $string): string;

    /**
     * @param string $string
     *
     * @return string
     */
    public function translateWeekDay(string $string): string;

    /**
     * @param string $string
     *
     * @return string
     */
    public function translateTimeShift(string $string): string;

    /**
     * Try to translate words to number.
     *
     * @param string $string
     *
     * @return int|null
     */
    public function wordsToNumber(string $string): ?int;

    /**
     * Parses absolute rules.
     *
     * @param string            $rule
     * @param array             $matches
     * @param DateTimeImmutable $datetime
     *
     * @return DateTimeImmutable
     */
    public function parseAbsolute(string $rule, array $matches, DateTimeImmutable $datetime): DateTimeImmutable;

    /**
     * Parses relative rules.
     *
     * @param string             $rule
     * @param array              $matches
     * @param \DateTimeImmutable $datetime
     *
     * @return \DateTimeImmutable
     */
    public function parseRelative(string $rule, array $matches, DateTimeImmutable $datetime): DateTimeImmutable;
}
