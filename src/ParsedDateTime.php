<?php

namespace wapmorgan\TimeParser;

use DateTimeImmutable;

class ParsedDateTime
{
    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var array
     */
    private $matches;

    /**
     * @var string
     */
    private $result;

    /**
     * @var string
     */
    private $language;

    public function __construct(DateTimeImmutable $date, array $matches, string $result, string $language)
    {
        $this->date     = $date;
        $this->matches  = $matches;
        $this->result   = $result;
        $this->language = $language;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }
}
