<?php declare(strict_types=1);

namespace App\Shortener;

use App\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;
use Sabre\Uri;
use Sabre\Uri\InvalidUriException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Shortener
{
    private Alphabet $alphabet;
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(Alphabet $alphabet, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->alphabet = $alphabet;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param Link $link
     *
     * @return array(string, Link)
     *
     * @throws ValidationException | InvalidUriException
     * @throws \Exception
     */
    public function shortifyLink(Link $link): array
    {
        $violations = $this->validator->validate($link);
        if (\count($violations) > 0) {
            throw new ValidationException('Failed to validate Link', $violations);
        }

        $this->em->beginTransaction();
        try {
            $uri = Uri\normalize($link->getUri());
            $dbLink = $this->em->getRepository(Link::class)->findByUri($uri);

            if (null !== $dbLink) {
                $link = $dbLink->setExpireAt($link->getExpireAt());
            }

            $this->em->persist($link->setUri($uri));
            $this->em->flush();
            $this->em->commit();

            return [$this->alphabet->encode($link->getId()), $link];
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @param string $path
     *
     * @return Link
     *
     * @throws \Exception
     */
    public function restoreLink(string $path): Link
    {
        $id = $this->alphabet->decode($path);

        return $this->em->getRepository(Link::class)->findActiveById($id);
    }
}
