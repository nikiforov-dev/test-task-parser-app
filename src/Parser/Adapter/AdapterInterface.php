<?php

namespace App\Parser\Adapter;

interface AdapterInterface
{
    /**
     * @return $this
     */
    public static function init(?int $amount = null): self;

    public function getAmount(): ?int;

    public function getSourceName(): string;

    public function getSourceMainUrl(): string;

    public function getRequestMethod(): string;

    public function getRequestBody(): array;

    public function getRequestHeaders(): array;

    public function getClientOptions(): array;

    public function extractInnerUrls(string $contents): array;

    /**
     * Should return empty array if it's not possible to parse page properly.
     */
    public function getContents(string $html, ?string $url = null): array;

    /**
     * @return string[]
     */
    public function getAllowedInnerUrlsPatterns(): array;

    public function getFormClass(): string;
}
