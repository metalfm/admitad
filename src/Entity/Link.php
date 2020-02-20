<?php declare(strict_types=1);

namespace App\Entity;

use App\Validator\Constraints\FutureDateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 */
class Link
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2048)
     * @Assert\Url
     * @Assert\NotBlank
     * @Assert\Length(max=2048)
     */
    private $uri;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotBlank
     * @FutureDateTime
     */
    private $expireAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
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
