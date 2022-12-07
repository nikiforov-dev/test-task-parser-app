<?php

namespace App\Parser\Adapter;

use App\Parser\Exception\ParsingException;
use DateTime;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class RbcAdapter implements AdapterInterface
{
    private const DEFAULT_AMOUNT = '15';
    private const URL_TEMPLATE   = "https://www.rbc.ru/v10/ajax/get-news-feed/project/rbcnews.uploaded/lastDate/%s/limit/%s";

    /**
     * @var string
     */
    private string $amountString;

    /**
     * @var int|null
     */
    private ?int $amount = null;

    /**
     * Use init instead
     */
    private function __construct(?int $amount = null)
    {
        $this->amount       = $amount;
        $this->amountString = $this->getStringAmount($this->amount);
    }



    /**
     * {@inheritdoc}
     */
    public static function init(?int $amount = null): self
    {
        return new static($amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceName(): string
    {
        return "rbc.ru";
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function getSourceMainUrl(): string
    {
        $currentTimeStamp = (new DateTime(timezone: new \DateTimeZone("UTC")))->getTimestamp();

        return sprintf(self::URL_TEMPLATE, $currentTimeStamp, $this->amountString);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function extractInnerUrls(string $contents): array
    {
        $json = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($json['items'])) {
            throw new ParsingException("No items in response!");
        }

        $result = [];

        foreach ($json['items'] as $item) {
            $result[] = $this->extractInnerUrlFromItem($item);
        }

        return $result;
    }

    /**
     * @param array $item
     * @return string
     *
     * @throws Throwable
     */
    private function extractInnerUrlFromItem(array $item): string
    {
        if (!isset($item['html'])) {
            throw new ParsingException("No html in item!");
        }

        $crawler = new Crawler($item['html']);

        $url = $crawler->filter('a')->attr('href');

        if (is_null($url)) {
            throw new ParsingException("Can't get inner url from item!");
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(string $html): array
    {


        return [];
    }

    /**
     * Returns string number representation from "01" to "99"
     *
     * @param int|null $amount
     *
     * @return string
     */
    private function getStringAmount(?int $amount): string
    {
        if (is_null($amount)) {
            return self::DEFAULT_AMOUNT;
        }

        if ($amount <= 1) {
            return '01';
        }

        if ($amount >= 99) {
            return '99';
        }

        if (intdiv($amount, 10) > 0) {
            return (string) $amount;
        }

        return "0{$amount}";
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestMethod(): string
    {
        return Request::METHOD_GET;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestBody(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestHeaders(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getClientOptions(): array
    {
        return [];
    }


}