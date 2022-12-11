<?php

namespace App\Repository;

use App\Entity\Article;
use App\Parser\DTO\RbcDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param RbcDTO[] $objects
     */
    public function createArticlesFromRbcDTOArray(array $objects)
    {
        $query = $this->createQueryBuilder('article')
            ->andWhere('article.hash = :hash')
            ->getQuery()
        ;

        $entityManager = $this->getEntityManager();

        foreach ($objects as $object) {
            $found = $query->execute(['hash' => $object->getHash()]);

            if (count($found) > 0) {
                continue;
            }

            $article = (new Article())
                ->setHash($object->getHash())
                ->setUrl($object->getUrl())
                ->setTitle($object->getTitle())
                ->setContent($object->getContent())
                ->setImageUrl($object->getImageUrl())
            ;

            $entityManager->persist($article);
        }

        $entityManager->flush();
    }

    /**
     * @return Article[]
     */
    public function getLastArticles(int $amount): array
    {
        return $this->createQueryBuilder('article')
            ->orderBy('article.id', 'DESC')
            ->setMaxResults($amount)
            ->getQuery()
            ->execute()
        ;
    }
}
