<?php

namespace App\Controller;

use App\DependencyInjection\InjectionTrait\ArticleRepositoryInjectionTrait;
use App\DependencyInjection\InjectionTrait\ParserInjectionTrait;
use App\Entity\Article;
use App\Parser\Adapter\AdapterInterface;
use App\Parser\Adapter\RbcAdapter;
use App\Parser\Exception\ParserRequestException;
use App\Parser\Exception\ParsingException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    use ArticleRepositoryInjectionTrait;
    use ParserInjectionTrait;

    private const RBC_RU = 'RBC.RU';

    private const POSSIBLE_PARSERS = [
        self::RBC_RU,
    ];

    private const DATETIME_FORMAT = 'yyyy-mm-dd hh:mm:ss';
    private const DEFAULT_AMOUNT = 15;

    public function rootAction(): Response
    {
        return $this->render('root.html.twig', [
            'possible_parsers' => self::POSSIBLE_PARSERS,
            'data_array' => $this->getLastArticles(self::DEFAULT_AMOUNT),
        ]);
    }

    public function articleAction(int $id): Response
    {
        /** @var Article $article */
        $article = $this->articleRepository->findOneBy(['id' => $id]);

        if (is_null($article)) {
            return new Response('Article not found', 404);
        }

        return $this->render('article.html.twig', ['article' => $article]);
    }

    /**
     * @throws ParserRequestException
     * @throws ParsingException
     */
    public function parseAction(Request $request): JsonResponse
    {
        try {
            $parser = (int) $request->query->get('parser') ?? 0;
            $amount = (int) $request->query->get('amount') ?? self::DEFAULT_AMOUNT;

            $adapter = $this->getParserAdapter($parser, $amount);
        } catch (\Throwable $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var AdapterInterface $adapter */
        $objects = $this->parser->parseSource($adapter);
        $this->articleRepository->createArticlesFromRbcDTOArray($objects);

        return new JsonResponse($this->getLastArticles($amount), Response::HTTP_OK);
    }

    private function getLastArticles(int $amount): array
    {
        $lastObjects = $this->articleRepository->getLastArticles($amount);

        $result = [];
        foreach ($lastObjects as $object) {
            $result[] = $this->prepareArticleObjectJson($object);
        }

        return $result;
    }

    private function prepareArticleObjectJson(Article $object): array
    {
        return [
            'id' => $object->getId(),
            'title' => $this->calculateShortTitle($object->getTitle()),
            'content' => $this->calculateShortContent($object->getContent(), $object->getTitle()),
        ];
    }

    private function calculateShortTitle(?string $title): ?string
    {
        if (is_null($title)) {
            return null;
        }

        if (strlen($title) > 200) {
            return mb_substr($title, 0, 200, 'UTF-8');
        }

        return $title;
    }

    private function calculateShortContent(string $content, ?string $title): string
    {
        $length = 0;

        if (!is_null($title)) {
            $length += strlen($title);
        }

        if ($length >= 200) {
            return '';
        }

        $subStringLength = 200 - $length;

        return mb_substr($content, 0, $subStringLength, 'UTF-8');
    }

    private function getParserAdapter(int $parser, int $amount): RbcAdapter
    {
        if (!isset(self::POSSIBLE_PARSERS[$parser])) {
            return RbcAdapter::init($amount);
        }

        return match (self::POSSIBLE_PARSERS[$parser]) {
            self::RBC_RU => RbcAdapter::init($amount),
            // TODO: add other parsers
        };
    }
}
