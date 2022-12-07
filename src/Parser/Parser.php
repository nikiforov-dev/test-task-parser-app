<?php

namespace App\Parser;

use App\DependencyInjection\InjectionTrait\LoggerInjectionTrait;
use App\Parser\Adapter\AdapterInterface;
use App\Parser\Exception\ParserRequestException;
use App\Parser\Exception\ParsingException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class Parser
{
    use LoggerInjectionTrait;

    /**
     * @var Client
     */
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param AdapterInterface $parsingAdapter
     * @return array
     *
     * @throws ParserRequestException
     * @throws ParsingException
     */
    public function parseSource(AdapterInterface $parsingAdapter): array
    {
        try {
            $result = $this->parsingAction($parsingAdapter);
        } catch (ParserRequestException $e) {
            throw $e;
        } catch (Throwable $e) {
            $errorMessage = "Can't parse source!";

            $this->logException($e, $errorMessage);

            throw new ParsingException($errorMessage . " " . $e->getMessage());
        }

        return $result;
    }



    /**
     * @param string $method
     * @param string $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return string
     *
     * @throws Throwable
     * @throws ParserRequestException
     */
    private function makeRequest(string $method, string $url, array $body = [], array $headers = [], array $options = []): string
    {
        if ($method !== Request::METHOD_GET) {
            $options = array_merge_recursive(
                $options,
                [
                    'body'    => $body,
                    'headers' => $headers
                ]
            );
        }

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (Throwable $e) {
            $errorMessage = "Can't make request to source!";

            $this->logException($e, $errorMessage);

            throw new ParserRequestException($errorMessage . " " . $e->getMessage());
        }


        if ($response->getStatusCode() !== 200) {
            $errorMessage = "Bad response status code!";

            $this->logger->error($errorMessage, [
                'status_code' => $response->getStatusCode(),
                'contents'    => $response->getBody()->getContents(),
                'reason'      => $response->getReasonPhrase(),
            ]);

            throw new ParserRequestException($errorMessage . " " . $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param AdapterInterface $parsingAdapter
     * @return array
     *
     * @throws ParserRequestException
     * @throws Throwable
     */
    private function parsingAction(AdapterInterface $parsingAdapter): array
    {
        $url     = $parsingAdapter->getSourceMainUrl();
        $method  = $parsingAdapter->getRequestMethod();
        $body    = $parsingAdapter->getRequestBody();
        $headers = $parsingAdapter->getRequestHeaders();
        $options = $parsingAdapter->getClientOptions();

        $this->logger->debug("Adapter data:", [
            'url'     => $url,
            'method'  => $method,
            'body'    => $body,
            'headers' => $headers,
            'options' => $options,
        ]);

        $contents  = $this->makeRequest($method, $url, $body, $headers);
        $innerUrls = $parsingAdapter->extractInnerUrls($contents);

        return [];
    }

    /**
     * @param Throwable $e
     * @param string $message
     */
    protected function logException(Throwable $e, string $message): void
    {
        $this->logger->error($message, [
            'error_message' => $e->getMessage(),
            'error_code'    => $e->getCode(),
            'error'         => $e
        ]);
    }
}