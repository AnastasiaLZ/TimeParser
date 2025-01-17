TimeParser
=====
A parser for date and time written in natural language for PHP.

[![Latest Stable Version](https://poser.pugx.org/wapmorgan/time-parser/v/stable)](https://packagist.org/packages/wapmorgan/time-parser) [![Total Downloads](https://poser.pugx.org/wapmorgan/time-parser/downloads)](https://packagist.org/packages/wapmorgan/time-parser) [![Latest Unstable Version](https://poser.pugx.org/wapmorgan/time-parser/v/unstable)](https://packagist.org/packages/wapmorgan/time-parser) [![License](https://poser.pugx.org/wapmorgan/time-parser/license)](https://packagist.org/packages/wapmorgan/time-parser) [![Build Status](https://travis-ci.org/wapmorgan/TimeParser.svg?branch=master)](https://travis-ci.org/wapmorgan/TimeParser)

1. [Installation](#installation)
2. [Usage](#usage)
3. [Languages support](#languages-support)
5. [ToDo](#languages-todo)

## Installation
The preferred way to install package is via composer:

```bash
composer require wapmorgan/time-parser
```

## Usage
Parse some input from user and receive a `DateTimeImmutable` object.

1. Create a Parser object
    ```php
    use wapmorgan\TimeParser\TimeParser;

    $parser = new TimeParser('all');
    ```

    First argument is a language. Applicable values:

    * `'all'` (by default) - scan for all available languages. Use it when you can not predict user's preferred language.
    * `'russian'` - scan only as string written in one language.
    * `array('english', 'russian')` - scan as english and then the rest as russian.

2. Parse string and return a `DateTimeImmutable` object. If second argument is `true`, method will return `false` when no date&time strings found. If third parameter is provided, then it is filled with the string obtained after all the transformations.
    ```php
    $datetime = $parser->parse(fgets(STDIN));
    // next call returns false
    $datetime = $parser->parse('abc', true);
    // $result will contains "we will come "
    $datetime = $parser->parse('We will come in a week', true, $result);
    ```
3. You can add your own custom language.
    ```php
    $parser->addLanguage(wapmorgan\TimeParser\Interfaces\LanguageInterface $language);
    ```

## Languages support
For this moment four languages supported: Russian, English, French and German. Two languages support is in progress: Chinese, Spanish.
Their rules are in `rules` catalog so you can improve TimeParser by adding new language or by improving existing one.  
You can use your own classes for each language, the class must implement interface `wapmorgan\TimeParser\Interfaces\LanguageInterface`. For example you can extend class `wapmorgan\TimeParser\Language`

Languages with examples of strings containing date&time:

| Language | Example                                                                                                                                                                                   |
|----------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| chinese  | 15:12:13 下星期一 下年 二月 経由15小时 経由10分钟 経由11秒钟 経由5年 経由2星期 経由1天 経由10月                                                                                                     |
| english  | 15 december 1977 at 15:12:13 next monday next year in february in 15 hours in 10 minutes in 11 seconds in 5 years in 2 weeks in 1 day in 10 months                                        |
| french   | 15:12:13 prochaine lundi prochaine année février bout de 15 heures bout de 10 minutes bout de 11 secondes bout de 5 années bout de 2 semaines bout de 1 jour bout de 10 mois              |
| german   | 15:12:13 nächsten montag nächsten jahr im februar nach 15 uhr nach 10 minuten nach 11 secunden nach 5 jahre nach 2 wochen nach 1 tag nach 10 monate                                       |
| russian  | 15 декабря 1977 года в 15:12:13 в следующий понедельник в следующем году в феврале через 15 часов через 10 минут через 11 секунд через 5 лет через 2 недели через 1 день через 10 месяцев |
| spanish  | 15:12:13 el próximo lunes en próximo año en febrero en 15 horas en 10 minutos en 11 segundos en 5 años en 2 semanas en 1 día en 10 meses                                                  |

For developing reasons you may would like to see process of parsing. To do this call related methods:

```php
$parser->setDebug(true);
// and
$parser->setDebug(false);
```

### Parsable substrings
To understand, how it works, look at substrings separately:

* **15 december 1977** - absolute date
* **at 15:12:13** - absolute time
* **next monday** or **this friday** - absolute date
* **next year** or **2016 year** - absolute date
* **in february** or **next month** - absolute date
* **next week** - absolute date
* **in 15 hours** - relative time
* **in 10 minutes** - relative time
* **in 11 seconds** - relative time
* **in 2 weeks** - relative date
* **in 1 day** - relative date
* **in 10 months** - relative date
* **через час** - relative date (russian, english only)
* **in a hour** - relative date (russian, english only)
* **через двадцать пять минут** - relative date (russian, english only)
* **in twenty five minutes** - relative date (russian, english only)
* **пять минут назад** - relative date (russian, english only)
* **five minutes ago** - relative date (russian, english only)
* **5 минут назад** - relative date (russian, english only)
* **5 minutes ago** - relative date (russian, english only)
* **вчера** - relative date (russian, english only)
* **yesterday** - relative date (russian, english only)
* **через 5 часов и пять минут** - relative combinations (russian, english only)
* **in 5 hours and 2 minutes** - relative combinations (russian, english only)

### Languages ToDo

- [x] Chinese - check hieroglyphs.
- [x] Spanish - check prepositions.
- [ ] Portuguese
- [ ] Arabic
- [ ] Korean
