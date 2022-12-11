<?php

namespace App\Parser\Adapter;

interface AdapterInterface
{
    /**
     * @return $this
     */
    public static function init(?int $amount = null): self;

    /**
     * @return int|null
     */
    public function getAmount(): ?int;

    /**
     * @return string
     */
    public function getSourceName(): string;

    /**
     * @return string
     */
    public function getSourceMainUrl(): string;

    /**
     * @return string
     */
    public function getRequestMethod(): string;

    /**
     * @return array
     */
    public function getRequestBody(): array;

    /**
     * @return array
     */
    public function getRequestHeaders(): array;

    /**
     * @return array
     */
    public function getClientOptions(): array;

    /**
     * @param string $contents
     *
     * @return array
     */
    public function extractInnerUrls(string $contents): array;

    /**
     * Should return empty array if it's not possible to parse page properly.
     *
     * @param string $html
     * @param string|null $url
     *
     * @return array
     */
    public function getContents(string $html, ?string $url = null): array;

    /**
     * @return string[]
     */
    public function getAllowedInnerUrlsPatterns(): array;

    /**
     * @return string
     */
    public function getFormClass(): string;
}
