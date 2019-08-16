<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser\Language;

use wapmorgan\TimeParser\Language;

class English extends Language
{
    public function __construct()
    {
        $data = static::findRuleByName('English');
        $data = static::validateData($data);

        parent::__construct(
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
}
