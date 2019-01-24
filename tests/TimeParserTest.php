<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use wapmorgan\TimeParser\TimeParser;

class TimeParserTest extends TestCase
{
    protected static $parsers = [];

    public static function setUpBeforeClass()
    {
        self::$parsers['all']     = new TimeParser('all');
        self::$parsers['russian'] = new TimeParser('russian');
        self::$parsers['english'] = new TimeParser('english');
    }

    /**
     * @dataProvider dataProviderEnglish()
     */
    public function testEnglish($string, $expected, $midnight = false)
    {
        $result = self::$parsers['english']->parse($string, true);

        $this->prepareDate($result, $expected, $midnight);
        $this->assertEquals($expected, $result);
    }

    public function dataProviderEnglish()
    {
        return [
            ['15 december 1977 year', '15 december 1977'],
            ['at 15:12:13', '15:12:13'],
            ['next monday', 'next monday', true],
            ['next year', '+1 year'],
            ['in february', 'february'],
            ['in 15 hours', '+15 hour'],
            ['in 10 minutes', '+10 minutes'],
            ['in 11 seconds', '+11 seconds'],
            ['in 5 years', '+5 years'],
            ['in 2 weeks', '+2 weeks'],
            ['in 1 day', '+1 day'],
            ['in 10 months', '+10 month'],
            ['tomorrow', '+1 day'],
            ['yesterday', '-1 day'],
            ['2 hours ago', '-2 hour'],
            ['10 years ago', '-10 year'],
            ['in twenty two days, twenty five hours and fifty five minutes', '+22 days +25 hours +55 minutes'],
            ['thirty first december 2018', '31 december 2018'],
            ['next week', '+1 week'],
            ['in next month', '+1 month'],
            ['next year', '+1 year'],
            ['last week', '-1 week'],
            ['in last month', '-1 month'],
            ['last year', '-1 year'],
            ['the string does not contain the date', false],
        ];
    }

    /**
     * @dataProvider dataProviderRussian()
     */
    public function testRussian($string, $expected, $midnight = false)
    {
        $result = self::$parsers['russian']->parse($string, true);

        $this->prepareDate($result, $expected, $midnight);
        $this->assertEquals($expected, $result);
    }

    public function dataProviderRussian()
    {
        return [
            ['15 декабря 1977 года', '15 december 1977'],
            ['в 15:12:13', '15:12:13'],
            ['в следующий понедельник', 'next monday', true],
            ['в следующем году', '+1 year'],
            ['в феврале', 'february'],
            ['через 15 часов', '+15 hour'],
            ['через 10 минут', '+10 minutes'],
            ['через 11 секунд', '+11 seconds'],
            ['через 5 лет', '+5 years'],
            ['через 2 недели', '+2 weeks'],
            ['через 1 день', '+1 day'],
            ['через 10 месяцев', '+10 month'],
            ['завтра', '+1 day'],
            ['вчера', '-1 day'],
            ['2 часа назад', '-2 hour'],
            ['10 лет назад', '-10 year'],
            ['через двадцать два дня, двадцать пять часов и пятьдесят пять минут', '+22 days +25 hours +55 minutes'],
            ['тридцать первого декабря 2018', '31 december 2018'],
            ['на следующей неделе', '+1 week'],
            ['в следующем месяце', '+1 month'],
            ['в следующем году', '+1 year'],
            ['на прошлой неделе', '-1 week'],
            ['в прошлом месяце', '-1 month'],
            ['в прошлом году', '-1 year'],
            ['в 10 утра', '10 am'],
            ['в 10 часов вечера', '10 pm'],
            ['в 2 ночи', '2 am'],
            ['в 2 часа дня', '2 pm'],
            ['строка не содержит дату', false],
        ];
    }

    /**
     * @dataProvider dataProviderStripWhitespace()
     */
    public function testStripWhitespace($string, $expected)
    {
        $string = self::$parsers['all']->stripWhitespace($string);

        $this->assertEquals($expected, $string);
    }

    public function dataProviderStripWhitespace()
    {
        return [
            [' TimeParser ', 'TimeParser'],
            ["\x20 Time \x20 Parser \x20", 'Time Parser'],                          // U+0020 SPACE
            ["\xc2\xa0 Time \xc2\xa0 Parser \xc2\xa0", 'Time Parser'],              // U+00A0 NO-BREAK SPACE
            ["\xe1\x9a\x80 Time \xe1\x9a\x80 Parser \xe1\x9a\x80", 'Time Parser'],  // U+1680 OGHAM SPACE MARK
            ["\xe2\x80\x80 Time \xe2\x80\x80 Parser \xe2\x80\x80", 'Time Parser'],  // U+2000 EN QUAD
            ["\xe2\x80\x81 Time \xe2\x80\x81 Parser \xe2\x80\x81", 'Time Parser'],  // U+2001 EM QUAD
            ["\xe2\x80\x82 Time \xe2\x80\x82 Parser \xe2\x80\x82", 'Time Parser'],  // U+2002 EN SPACE
            ["\xe2\x80\x83 Time \xe2\x80\x83 Parser \xe2\x80\x83", 'Time Parser'],  // U+2003 EM SPACE
            ["\xe2\x80\x84 Time \xe2\x80\x84 Parser \xe2\x80\x84", 'Time Parser'],  // U+2004 THREE-PER-EM SPACE
            ["\xe2\x80\x85 Time \xe2\x80\x85 Parser \xe2\x80\x85", 'Time Parser'],  // U+2005 FOUR-PER-EM SPACE
            ["\xe2\x80\x86 Time \xe2\x80\x86 Parser \xe2\x80\x86", 'Time Parser'],  // U+2006 SIX-PER-EM SPACE
            ["\xe2\x80\x87 Time \xe2\x80\x87 Parser \xe2\x80\x87", 'Time Parser'],  // U+2007 FIGURE SPACE
            ["\xe2\x80\x88 Time \xe2\x80\x88 Parser \xe2\x80\x88", 'Time Parser'],  // U+2008 PUNCTUATION SPACE
            ["\xe2\x80\x89 Time \xe2\x80\x89 Parser \xe2\x80\x89", 'Time Parser'],  // U+2009 THIN SPACE
            ["\xe2\x80\x8a Time \xe2\x80\x8a Parser \xe2\x80\x8a", 'Time Parser'],  // U+200A HAIR SPACE
            ["\xe2\x80\xaf Time \xe2\x80\xaf Parser \xe2\x80\xaf", 'Time Parser'],  // U+202F NARROW NO-BREAK SPACE
            ["\xe2\x81\x9f Time \xe2\x81\x9f Parser \xe2\x81\x9f", 'Time Parser'],  // U+205F MEDIUM MATHEMATICAL SPACE
            ["\xe3\x80\x80 Time \xe3\x80\x80 Parser \xe3\x80\x80", 'Time Parser'],  // U+3000 IDEOGRAPHIC SPACE
        ];
    }

    protected function prepareDate(&$result, &$expected, $midnight)
    {
        $date = new DateTimeImmutable();

        if ($result !== false) {
            if ($midnight) {
                $result = $result->setTime(0, 0);
            }

            $result = $result->format('r');
        }

        if ($expected !== false) {
            $expected = $date->modify($expected)->format('r');
        }
    }
}
