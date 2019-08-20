<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

namespace wapmorgan\TimeParser\Language;

use Psr\Log\LoggerInterface;
use wapmorgan\TimeParser\Exceptions\TimeParserException;
use wapmorgan\TimeParser\Language;

class Spanish extends Language
{
    /**
     * Spanish constructor.
     * @param LoggerInterface $logger
     * @throws TimeParserException
     */
    public function __construct(LoggerInterface $logger)
    {
        $data = static::findRuleByName('Spanish');
        $data = static::validateData($data);

        parent::__construct(
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
}
