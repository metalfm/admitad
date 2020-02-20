<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * @param string $uri
     *
     * @return Link|null
     *
     * @throws NonUniqueResultException
     */
    public function findByUri(string $uri): ?Link
    {
        try {
            $link = $this
                ->createQueryBuilder('l')
                ->andWhere('l.uri = :uri')
                ->setParameter('uri', $uri)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            throw $e;
        }

        return $link;
    }

    /**
     * @param int $id
     *
     * @return Link
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findActiveById(int $id): Link
    {
        return $this
            ->createQueryBuilder('l')
            ->andWhere('l.id = :id')
            ->andWhere('l.expireAt > :expire_at')
            ->setParameter('id', $id)
            ->setParameter('expire_at', new \DateTime())
            ->getQuery()
            ->getSingleResult();
    }
}
