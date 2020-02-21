<?php declare(strict_types=1);

namespace App\Tests\Shortener;

use App\Entity\Link;
use App\Shortener\Shortener;
use App\Shortener\ValidationException;
use Doctrine\ORM\NoResultException;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShortenerTest extends KernelTestCase
{
    use MatchesSnapshots;

    private Shortener $shortener;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->shortener = self::$container->get(Shortener::class);
    }

    /**
     * @return \Generator
     *
     * @throws \Exception
     */
    public function providerInvalidLink(): \Generator
    {
        yield [(new Link())->setUri('')->setExpireAt(new \DateTimeImmutable('2010-01-01 00:00:00'))];
        yield [(new Link())->setUri('invalid-uri')->setExpireAt(new \DateTimeImmutable('2010-01-01 00:00:00'))];
        yield [(new Link())->setUri('http://ya.ru/?'.\str_repeat('query=value&', 1000))->setExpireAt(new \DateTimeImmutable('2010-01-01 00:00:00'))];
    }

    /**
     * @dataProvider providerInvalidLink
     *
     * @param Link $link
     *
     * @throws \Sabre\Uri\InvalidUriException
     */
    public function testShortifyValidation(Link $link): void
    {
        try {
            $this->shortener->shortifyLink($link);
        } catch (ValidationException $e) {
            $this->assertMatchesSnapshot((array)$e->getViolations());
        }
    }

    /**
     * @throws ValidationException
     * @throws \Sabre\Uri\InvalidUriException
     */
    public function testExistLink(): void
    {
        $link = (new Link())->setUri('http://ya.ru/')->setExpireAt(new \DateTimeImmutable('+1 day'));
        /** @var string $short */
        /** @var Link $link */
        [$short, $link] = $this->shortener->shortifyLink($link);

        $this->assertEquals('b', $short);
        $this->assertEquals(1, $link->getId());
    }

    /**
     * @throws ValidationException
     * @throws \Sabre\Uri\InvalidUriException
     */
    public function testNewLink(): void
    {
        $link = (new Link())->setUri('http://new-link.ru/')->setExpireAt(new \DateTimeImmutable('+1 day'));
        /* @var string $short */
        /** @var Link $link */
        [, $link] = $this->shortener->shortifyLink($link);

        $this->assertGreaterThan(2, $link->getId());
    }

    /**
     * @throws \Exception
     */
    public function testRestoreLink(): void
    {
        $link = $this->shortener->restoreLink('b');
        $this->assertMatchesSnapshot($link);
    }

    /**
     * @throws \Exception
     */
    public function testExpiredRestore(): void
    {
        $this->expectException(NoResultException::class);
        $this->shortener->restoreLink('c');
    }
}
