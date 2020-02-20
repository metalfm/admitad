<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Link
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $expireAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setExpireAt(\DateTimeImmutable $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getExpireAt(): \DateTimeImmutable
    {
        return $this->expireAt;
    }
}
