<?php

namespace App\Parser\DTO;

class RbcDTO
{
    /**
     * @var string|null
     */
    private ?string $url = null;

    /**
     * @var string
     */
    private string $hash;

    /**
     * @var string|null
     */
    private ?string $title = null;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string|null
     */
    private ?string $imageUrl = null;

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return $this
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @return $this
     */
    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return $this
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
