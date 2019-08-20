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
    public function getName(): string
    {
        return $this->name;
    }

    public function getAbsoluteRules(): array
    {
        return $this->absoluteRules;
    }

    public function getRelativeRules(): array
    {
        return $this->relativeRules;
    }

    public function getRules(): array
    {
        return [
            'absolute' => $this->getAbsoluteRules(),
            'relative' => $this->getRelativeRules(),
        ];
    }

    public function getMonths(): array
    {
        return $this->months;
    }

    public function getPronouns(): array
    {
        return $this->pronouns;
    }

    public function getUnits(): array
    {
        return $this->units;
    }

    public function getWeekDays(): array
    {
        return $this->weekDays;
    }
}
