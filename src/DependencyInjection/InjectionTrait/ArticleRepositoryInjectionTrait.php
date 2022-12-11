<?php

namespace App\DependencyInjection\InjectionTrait;

use App\Repository\ArticleRepository;

trait ArticleRepositoryInjectionTrait
{
    protected ArticleRepository $articleRepository;

    /**
     * @required
     *
     * @return $this
     */
    public function setArticleRepository(ArticleRepository $articleRepository): self
    {
        $this->articleRepository = $articleRepository;

        return $this;
    }
}
