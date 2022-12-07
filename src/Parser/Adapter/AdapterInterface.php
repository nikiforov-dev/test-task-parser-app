<?php

namespace App\Parser\Adapter;

interface AdapterInterface
{
    /**
     * @param int|null $amount
     *
     * @return $this
     */
    public static function init(?int $amount = null): self;

    /**
     * @return string
     */
    public function getSourceName(): string;

    /**
     * @param array $options
     *
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
     * @param string $html
     *
     * @return array
     */
    public function getContents(string $html): array;
}