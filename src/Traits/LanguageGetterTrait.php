<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser\Traits;

trait LanguageGetterTrait
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbsoluteRules()
    {
        return $this->absoluteRules;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativeRules()
    {
        return $this->relativeRules;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return [
            'absolute' => $this->getAbsoluteRules(),
            'relative' => $this->getRelativeRules(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * {@inheritdoc}
     */
    public function getPronouns()
    {
        return $this->pronouns;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeekDays()
    {
        return $this->weekDays;
    }
}
