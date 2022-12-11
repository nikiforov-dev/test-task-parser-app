<?php

namespace App\Parser\Adapter;

use App\Parser\Exception\ParsingException;
use App\Parser\Form\RbcForm;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class RbcAdapter implements AdapterInterface
{
    private const DEFAULT_AMOUNT = 15;
    private const URL_TEMPLATE = 'https://www.rbc.ru/v10/ajax/get-news-feed/project/rbcnews.uploaded/lastDate/%s/limit/99';

    private const RBC_URL_PATTERN = '/^https:\\/\\/www.rbc.ru\\/.*/';
    private const EXCEPTION_URL_PATTERN = '/^https:\\/\\/www.rbc.ru\\/life\\/news\\/.*/';

    private ?int $amount = self::DEFAULT_AMOUNT;

    /**
     * Use init instead.
     */
    private function __construct(?int $amount = null)
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(): ?int
    {
        return $this->amount;
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
        return 'rbc.ru';
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function getSourceMainUrl(): string
    {
        $currentTimeStamp = (new \DateTime(timezone: new \DateTimeZone('UTC')))->getTimestamp();

        return sprintf(self::URL_TEMPLATE, $currentTimeStamp);
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
            throw new ParsingException('No items in response!');
        }

        $result = [];

        foreach ($json['items'] as $item) {
            $result[] = $this->extractInnerUrlFromItem($item);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(string $html, ?string $url = null): array
    {
        $domCrawler = new Crawler($html);

        if (!is_null($url) && preg_match(self::EXCEPTION_URL_PATTERN, $url)) {
            return $this->processExceptionUrl($domCrawler, $url);
        }

        $articleTitleNode = $domCrawler->filter('div.article__header__title');
        $articleTextNode = $domCrawler->filter('div.article__text');

        if (0 == $articleTitleNode->count() && 0 == $articleTextNode->count()) {
            return [];
        }

        $title = $articleTitleNode->filter('h1.article__header__title-in')->each(function (Crawler $node) {
            return $node->text();
        });

        $title = trim(implode("\n", $title));

        if (empty($title)) {
            $title = null;
        }

        $overview = $articleTextNode->filter('div.article__text__overview > span')->each(function (Crawler $node) {
            return $node->text();
        });

        $overview = trim(implode("\n", $overview));

        $paragraph = $articleTextNode->filter('p')->each(function (Crawler $node) {
            return $node->text();
        });

        $paragraph = trim(implode("\n\n", $paragraph));

        if (!empty($overview)) {
            $text = "{$overview}\n\n{$paragraph}";
        } else {
            $text = $paragraph;
        }

        $imageUrl = $articleTextNode->filter('img')->each(function (Crawler $node) {
            return $node->attr('src');
        });

        if (!empty($imageUrl)) {
            $imageUrl = $imageUrl[0];
        } else {
            $imageUrl = null;
        }

        $hash = md5($title.$text);

        return $this->prepareResult($url, $hash, $title, $text, $imageUrl);
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

    /**
     * {@inheritdoc}
     */
    public function getFormClass(): string
    {
        return RbcForm::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedInnerUrlsPatterns(): array
    {
        return [
            self::RBC_URL_PATTERN,
        ];
    }

    /**
     * @throws ParsingException
     */
    private function extractInnerUrlFromItem(array $item): string
    {
        if (!isset($item['html'])) {
            throw new ParsingException('No html in item!');
        }

        $crawler = new Crawler($item['html']);

        $url = $crawler->filter('a')->attr('href');

        if (is_null($url)) {
            throw new ParsingException("Can't get inner url from item!");
        }

        return $url;
    }

    /**
     * @param Crawler $domCrawler
     * @param string $url
     *
     * @return array
     */
    private function processExceptionUrl(Crawler $domCrawler, string $url): array
    {
        $title = $domCrawler->filter('header.article-entry > h1')->each(function (Crawler $node) {
            return $node->text();
        });

        $title = array_merge($title, $domCrawler->filter('header.article-entry > span.article-entry-subtitle')->each(function (Crawler $node) {
            return $node->text();
        }));

        $title = trim(implode($title));

        $text = $domCrawler->filter('article.article-feature-item > p.paragraph')->each(function (Crawler $node) {
            return $node->text();
        });

        $text = trim(implode($text));

        $imageUrl = $domCrawler->filter('div[itemprop="image"] > link')->each(function (Crawler $node) {
            return $node->attr('href');
        });

        if (!empty($imageUrl)) {
            $imageUrl = $imageUrl[0];
        } else {
            $imageUrl = null;
        }

        $hash = md5($title.$text);

        return $this->prepareResult($url, $hash, $title, $text, $imageUrl);
    }

    /**
     * @param string $url
     * @param string $hash
     * @param string $title
     * @param string $text
     * @param mixed $imageUrl
     *
     * @return array
     */
    private function prepareResult(string $url, string $hash, string $title, string $text, mixed $imageUrl): array
    {
        return [
            'url' => $url,
            'hash' => $hash,
            'title' => $title,
            'content' => $text,
            'imageUrl' => $imageUrl,
        ];
    }
}
