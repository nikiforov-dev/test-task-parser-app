<?php

namespace App\Parser;

use App\DependencyInjection\InjectionTrait\FormFactoryInjectionTrait;
use App\DependencyInjection\InjectionTrait\FormHandlerInjectionTrait;
use App\DependencyInjection\InjectionTrait\LoggerInjectionTrait;
use App\Parser\Adapter\AdapterInterface;
use App\Parser\Exception\ParserRequestException;
use App\Parser\Exception\ParsingException;
use App\Utils\Form\Exception\FormException;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

class Parser
{
    use LoggerInjectionTrait;
    use FormFactoryInjectionTrait;
    use FormHandlerInjectionTrait;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws ParserRequestException
     * @throws ParsingException
     */
    public function parseSource(AdapterInterface $parsingAdapter): array
    {
        try {
            $result = $this->parsingAction($parsingAdapter);
        } catch (ParserRequestException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $errorMessage = "Can't parse source!";

            $this->logException($e, $errorMessage);

            throw new ParsingException($errorMessage.' '.$e->getMessage());
        }

        return $result;
    }

    protected function logException(\Throwable $e, string $message): void
    {
        $this->logger->error($message, [
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'error' => $e,
        ]);
    }

    /**
     * @throws \Throwable
     * @throws ParserRequestException
     */
    private function makeRequest(string $method, string $url, array $body = [], array $headers = [], array $options = []): string
    {
        if (Request::METHOD_GET !== $method) {
            $options = array_merge_recursive(
                $options,
                [
                    'body' => $body,
                    'headers' => $headers,
                ]
            );
        }

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (\Throwable $e) {
            $errorMessage = "Can't make request to source!";

            $this->logException($e, $errorMessage);

            throw new ParserRequestException($errorMessage.' '.$e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            $errorMessage = 'Bad response status code!';

            $this->logger->error($errorMessage, [
                'status_code' => $response->getStatusCode(),
                'contents' => $response->getBody()->getContents(),
                'reason' => $response->getReasonPhrase(),
            ]);

            throw new ParserRequestException($errorMessage.' '.$response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * @throws ParserRequestException
     * @throws \Throwable
     */
    private function parsingAction(AdapterInterface $parsingAdapter): array
    {
        $this->logger->debug('Adapter data:', [
            'amount' => $parsingAdapter->getAmount(),
            'url' => $parsingAdapter->getSourceMainUrl(),
            'method' => $parsingAdapter->getRequestMethod(),
            'body' => $parsingAdapter->getRequestBody(),
            'headers' => $parsingAdapter->getRequestHeaders(),
            'options' => $parsingAdapter->getClientOptions(),
            'allowedPatterns' => $parsingAdapter->getAllowedInnerUrlsPatterns(),
        ]);

        $contents = $this->makeRequest(
            $parsingAdapter->getRequestMethod(),
            $parsingAdapter->getSourceMainUrl(),
            $parsingAdapter->getRequestBody(),
            $parsingAdapter->getRequestHeaders(),
            $parsingAdapter->getClientOptions()
        );

        $innerUrls = $parsingAdapter->extractInnerUrls($contents);

        $allowedInnerUrls = $this->filterUrls($parsingAdapter->getAllowedInnerUrlsPatterns(), $innerUrls);

        return $this->processInnerUrls($parsingAdapter, $allowedInnerUrls);
    }

    private function filterUrls(array $allowedPatterns, array $innerUrls): array
    {
        if (empty($allowedPatterns)) {
            return $innerUrls;
        }

        $allowedInnerUrls = [];

        foreach ($innerUrls as $innerUrl) {
            foreach ($allowedPatterns as $allowedPattern) {
                if (!preg_match($allowedPattern, $innerUrl)) {
                    continue;
                }

                $allowedInnerUrls[] = $innerUrl;
            }
        }

        return $allowedInnerUrls;
    }

    /**
     * @throws ParserRequestException
     * @throws \Throwable
     */
    private function processInnerUrls(AdapterInterface $parsingAdapter, array $innerUrls): array
    {
        $result = [];

        $counter = 0;
        foreach ($innerUrls as $innerUrl) {
            if (!is_null($parsingAdapter->getAmount()) && $counter == $parsingAdapter->getAmount()) {
                break;
            }

            $responseContent = $this->makeRequest(
                $parsingAdapter->getRequestMethod(),
                $innerUrl,
                $parsingAdapter->getRequestBody(),
                $parsingAdapter->getRequestHeaders(),
                $parsingAdapter->getClientOptions()
            );

            $resultItem = $parsingAdapter->getContents($responseContent, $innerUrl);

            if (empty($resultItem)) {
                $this->logger->alert('Cant parse page!', [
                    'url' => $innerUrl,
                ]);

                continue;
            }

            $result[] = $this->createObjectFromArray($parsingAdapter, $resultItem);

            ++$counter;
        }

        return $result;
    }

    /**
     * @throws FormException
     */
    private function createObjectFromArray(AdapterInterface $adapter, array $item): object
    {
        $form = $this->formFactory->create($adapter->getFormClass());

        return $this->formHandler->handleForm($form, $item);
    }
}
