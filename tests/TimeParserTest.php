<?php

/**
 * TimeParser (https://wapmorgan.github.io/TimeParser/)
 *
 * @link      https://github.com/wapmorgan/TimeParser
 * @copyright Copyright (c) 2014-2019 wapmorgan
 * @license   https://github.com/wapmorgan/TimeParser/blob/master/LICENSE (MIT License)
 */

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use wapmorgan\TimeParser\Exceptions\TimeParserException;
use wapmorgan\TimeParser\ParsedDateTime;
use wapmorgan\TimeParser\TimeParser;

class TimeParserTest extends TestCase
{
    /**
     * @var TimeParser
     */
    protected $parser;

    /**
     * @throws TimeParserException
     */
    public function setUp()
    {
        /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $loggerStub */
        $loggerStub   = $this->createMock(LoggerInterface::class);
        $this->parser = new TimeParser($loggerStub);
    }

    /**
     * @dataProvider dataProviderCommon()
     * @param string $string
     * @param string $expected
     * @throws TimeParserException
     * @throws Exception
     */
    public function testCommon(string $string, ?string $expected)
    {
        $result = $this->parser->parse($string);

        $this->checkDates($expected, $result, 'Common');
    }

    public function dataProviderCommon()
    {
        return [
            1 => ['at 15:12:13', '15:12:13'],
            2 => ['в 15:12:13', '15:12:13'],
        ];
    }

    /**
     * @dataProvider dataProviderEnglish()
     * @param string $string
     * @param string $expected
     * @throws TimeParserException
     * @throws Exception
     */
    public function testEnglish(string $string, ?string $expected)
    {
        $result = $this->parser->parse($string);

        $this->checkDates($expected, $result, 'English');
    }

    public function dataProviderEnglish()
    {
        return [
            0  => ['15 december 1977 year', '15 december 1977'],
            1  => ['next monday', 'next monday'],
            2  => ['next year', '+1 year'],
            3  => ['in february', 'february'],
            4  => ['in 15 hours', '+15 hour'],
            5  => ['in 10 minutes', '+10 minutes'],
            6  => ['in 11 seconds', '+11 seconds'],
            7  => ['in 5 years', '+5 years'],
            8  => ['in 2 weeks', '+2 weeks'],
            9  => ['in 1 day', '+1 day'],
            10 => ['in 10 months', '+10 month'],
            11 => ['tomorrow', '+1 day'],
            12 => ['yesterday', '-1 day'],
            13 => ['2 hours ago', '-2 hour'],
            14 => ['10 years ago', '-10 year'],
            15 => ['in twenty two days, twenty five hours and fifty five minutes', '+22 days +25 hours +55 minutes'],
            16 => ['thirty first december 2018', '31 december 2018'],
            17 => ['next week', '+1 week'],
            18 => ['in next month', '+1 month'],
            19 => ['next year', '+1 year'],
            20 => ['last week', '-1 week'],
            21 => ['in last month', '-1 month'],
            22 => ['last year', '-1 year'],
            23 => ['the string does not contain the date', null],
            24 => ['Tomorrow morning, please.', 'tomorrow 9 am'],
            25 => ['Could you please call back around 9 am.', 'today 9 am'],
            26 => ['Shall I move it to next Thursday?', 'next week thursday'],
            27 => ['next sunday', 'next week sunday'],
            28 => ['august the 20th', '20 august'],
            29 => ['Will it suit this Friday?', 'friday'],
            30 => ['Let\'s have a call in a weeks time.', '+1 week'],
            31 => ['I suggest we meet next week.', '+1 week'],
            32 => ['I ordered it yesterday', '-1 day'],
            33 => ['Last week it was supposed to be delivered.', '-1 week'],
            34 => ['I was in your office the day before yesterday.', '-2 days'],
            35 => ['I\'ll be ready sometime next month.', '+1 month'],
            36 => ['Should I call at half 9?', '9:00 -30 min'],
        ];
    }

    /**
     * @dataProvider dataProviderRussian()
     * @param string      $string
     * @param string      $expected
     * @param string|null $initial
     * @throws TimeParserException
     */
    public function testRussian(string $string, ?string $expected, ?string $initial = null)
    {
        $initialDateTime = new DateTimeImmutable($initial);
        $result          = $this->parser->parse($string, $initialDateTime);

        $this->checkDates($expected, $result, 'Russian', $initialDateTime);
    }

    public function dataProviderRussian()
    {
        return [
            0  => ['15 декабря 1977 года', '15 december 1977'],
            2  => ['в следующий понедельник', 'next monday'],
            3  => ['в следующем году', '+1 year'],
            4  => ['в феврале', 'february'],
            5  => ['через 15 часов', '+15 hour'],
            6  => ['через 10 минут', '+10 minutes'],
            7  => ['через 11 секунд', '+11 seconds'],
            8  => ['через 5 лет', '+5 years'],
            9  => ['через 2 недели', '+2 weeks'],
            10 => ['через 1 день', '+1 day'],
            11 => ['через 10 месяцев', '+10 month'],
            12 => ['завтра', '+1 day'],
            13 => ['вчера', '-1 day'],
            14 => ['2 часа назад', '-2 hour'],
            15 => ['10 лет назад', '-10 year'],
            16 => ['через двадцать два дня, двадцать пять часов и пятьдесят пять минут', '+22 days +25 hours +55 minutes'],
            17 => ['тридцать первого декабря 2018', '31 december 2018'],
            18 => ['на следующей неделе', '+1 week'],
            19 => ['в следующем месяце', '+1 month'],
            20 => ['в следующем году', '+1 year'],
            21 => ['на прошлой неделе', '-1 week'],
            22 => ['в прошлом месяце', '-1 month'],
            23 => ['в прошлом году', '-1 year'],
            24 => ['в 10 утра', '10 am'],
            25 => ['в 10 часов вечера', '10 pm'],
            26 => ['в 2 ночи', '2 am'],
            27 => ['в 2 часа дня', '2 pm'],
            28 => ['строка не содержит дату', null],
            29 => ['Завтра утром', 'tomorrow 9 am'],
            30 => ['сегодня, часиков в 9', 'today 9 am'],
            31 => ['перенести на следующий четверг?', 'next week thursday'],
            32 => ['Может быть перенести на следующий воскресенье?', 'next week sunday'],
            33 => ['Давайте числа двадцатого августа', '20 august'],
            34 => ['Удобно будет в пятницу', 'friday'],
            35 => ['Предлагаю созвониться через неделю', '+1 week'],
            36 => ['Предлагаю встретиться через недельку', '+1 week'],
            37 => ['Заказ делал вчера', '-1 day'],
            38 => ['Неделю назад должны были привезти', '-1 week'],
            39 => ['Я был у Вас в офисе позавчера', '-2 days'],
            40 => ['Где-нибудь через месяц я буду готов', '+1 month'],
            41 => ['Может быть позвонить в пол 9?', '9:00 -30 min'],
            42 => ['через 3 дня', '+3 days'],
            43 => ['послезавтра в обед', '+2 days noon'],
            44 => ['завтра в 2 часа дня', 'tomorrow 2 pm'],
            45 => ['через 7 дней', '+7 days'],
            46 => ['18 декабря в 18:15', '18:15 18 december'],
            47 => ['18 декабря в 7 вечера', '19:00 18 december'],
            48 => ['18 декабря 2009 в 6:00', '6:00 18 december 2009'],
            49 => ['восемнадцатого января в обед', '18 january noon'],
            50 => ['через 3 дня', '16.08.2019', '13.08.2019'],
        ];
    }

    /**
     * @dataProvider dataProviderStripWhitespace()
     * @param string $string
     * @param string $expected
     */
    public function testStripWhitespace(string $string, string $expected)
    {
        $string = $this->parser->stripWhitespace($string);

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

    /**
     * @dataProvider dataProviderFrench()
     * @param string $string
     * @param string $expected
     * @throws TimeParserException
     * @throws Exception
     */
    public function testFrench(string $string, ?string $expected)
    {
        $result = $this->parser->parse($string);

        $this->checkDates($expected, $result, 'French');
    }

    public function dataProviderFrench()
    {
        return [
            0  => ['Demain matin s\'il vous plait', 'tomorrow 9 am'],
            1  => ['Pourriez-vous rappeler vers 9 heures s\'il vous plaît.', 'today 9 am'],
            2  => ['Pouvons-nous reporter notre conversation à jeudi prochaine?', 'next week thursday'],
            3  => ['le 20 août', '20 august'],
            4  => ['Le vendredi me convient bien', 'friday'],
            5  => ['Je propose de téléphoner dans une semaine', '+1 week'],
            6  => ['Je suggère de se rencontrer dans une semaine', '+1 week'],
            7  => ['J\'ai fait une commande hier', '-1 day'],
            8  => ['Il était censé être livré à la semaine dernière', '-1 week'],
            9  => ['J\'étais dans votre bureau avant-hier.', '-2 days'],
            10 => ['Je serai prêt environ dans un mois', '+1 month'],
            11 => ['après trois mois', '+3 month'],
            12 => ['Dois-je vous appeler à huit heures et 9?', '9:00 -30 min'],
            13 => ['Il y a 5 semaines', '-5 week'],
        ];
    }

    /**
     * @dataProvider dataProviderGerman()
     * @param string $string
     * @param string $expected
     * @throws TimeParserException
     * @throws Exception
     */
    public function testGerman(string $string, ?string $expected)
    {
        $result = $this->parser->parse($string);

        $this->checkDates($expected, $result, 'German');
    }

    public function dataProviderGerman()
    {
        return [
            0  => ['Morgen frueh bitte', 'tomorrow 9 am'],
            1  => ['Rufen Sie bitte heute spaeter an, circa am 9 Uhr.', 'today 9 am'],
            2  => ['Koennen wir vielleicht unseren Gespraech am nächsten Donnerstag verschieben?', 'next week thursday'],
            3  => ['Lassen wir im 20 August, dazu kommen', '20 august'],
            4  => ['Freitag passt super', 'friday'],
            5  => ['Ich schlage vor am naechste Woche per Handy noch wieder zu kommunizieren', '+1 week'],
            6  => ['Treffen wir uns am naechste Woche', '+1 week'],
            7  => ['Ich hab\'s gestern bestellt', '-1 day'],
            8  => ['Es soll letzte Woche geliefert wurde', '-1 week'],
            9  => ['I war vorgestern bei Ihnen im Buero.', '-2 days'],
            10 => ['Ich bin irgendwie nach einem Monat dazu bereit ', '+1 month'],
            11 => ['Vielleicht macht es Sinn um halb 9 anzurufen?', '9:00 -30 min'],
        ];
    }

    /**
     * @dataProvider dataProviderSpanish()
     * @param string $string
     * @param string $expected
     * @throws TimeParserException
     * @throws Exception
     */
    public function testSpanish(string $string, ?string $expected)
    {
        $result = $this->parser->parse($string);

        $this->checkDates($expected, $result, 'Spanish');
    }

    public function dataProviderSpanish()
    {
        return [
            0  => ['Hablemos mañana por la mañana, por favor', 'tomorrow 9 am'],
            1  => ['¿Podría volver a llamarme hoy a las 9?', 'today 9 am'],
            2  => ['¿Podríamos posponerlo para el jueves que viene?', 'next week thursday'],
            3  => ['Vamos hacerlo en veinte de Agosto.', '20 august'],
            4  => ['El viernes parece estar bien.', 'friday'],
            5  => ['Volvamos a hablar por teléfono en una semana, si le conviene.', '+1 week'],
            6  => ['Sugiero que nos reunamos en una semana.', '+1 week'],
            7  => ['Hice el pedido ayer', '-1 day'],
            8  => ['Deberían haberlo traído la semana pasada', '-1 week'],
            9  => ['Llegé a la oficina anteayer.', '-2 days'],
            10 => ['En un mes estaré listo.', '+1 month'],
            11 => ['¿Cómo le parece llamar a las 9 y media?', '9:00 -30 min'],
        ];
    }

    /**
     * @param string|null            $string
     * @param DateTimeImmutable|null $initialDateTime
     * @return string|null
     * @throws Exception
     */
    protected function prepareDate(?string $string, ?DateTimeImmutable $initialDateTime = null): ?string
    {
        if (is_null($string)) {
            return null;
        }

        $date = $initialDateTime ? $initialDateTime : new DateTimeImmutable();

        return $date->modify($string)->format('d.m.Y H:i');
    }

    /**
     * @param string|null            $expected
     * @param ParsedDateTime|null    $result
     * @param string                 $expectedLang
     * @param DateTimeImmutable|null $initialDateTime
     * @throws Exception
     */
    private function checkDates(?string $expected, ?ParsedDateTime $result, string $expectedLang, ?DateTimeImmutable $initialDateTime = null)
    {
        $this->assertEquals($this->prepareDate($expected, $initialDateTime), $result ? $result->getDate()->format('d.m.Y H:i') : null);
        $this->assertEquals($result ? $expectedLang : null, $result ? $result->getLanguage() : null);
    }
}
