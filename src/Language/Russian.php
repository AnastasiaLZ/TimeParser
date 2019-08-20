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

class Russian extends Language
{
    // const MONTH     = '(?<month>янв(аря)?|фев(раля)?|мар(та)?|апр(еля)?|ма[йя]|июня?|июля?|авг(уста)?|сен(тября)?|окт(ября)?|ноя(бря)?|дек(абря)?)';
    // const MONTH2    = '(?<month>янв(аре)?|фев(рале)?|мар(те)?|апр(еле)?|ма[йе]|июне?|июле?|авг(усте)?|сен(тябре)?|окт(ябре)?|ноя(бре)?|дек(абре)?)';
    // const WEEK_DAYS = '(?<weekday>п(онедельник|н)|вт(орник)?|ср(еду)?|ч(етверг|т)|п(ятницу|т)|с(убботу|б)|в(оскресен[ьи]е|c))';
    // const TIME      = '(?<hour>\d{1,2})( час(а|ов)?|\:(?<min>\d{2})(\:(?<sec>\d{2}))?)';

    /**
     * Russian constructor.
     * @param LoggerInterface $logger
     * @throws TimeParserException
     */
    public function __construct(LoggerInterface $logger)
    {
        $data = static::findRuleByName('Russian');
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
