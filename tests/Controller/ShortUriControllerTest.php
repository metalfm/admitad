<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShortUriControllerTest extends WebTestCase
{
    use MatchesSnapshots;

    private ?EntityManagerInterface $em;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = self::$container->get(EntityManagerInterface::class);
    }

    public function testSuccessShortify(): void
    {
        $this->client->request('POST', '/', [
            'uri' => 'http://ya.ru',
            'expire_at' => '2025-01-01 23:23:21',
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertMatchesSnapshot($this->client->getResponse()->getContent());
    }

    public function testValidationShortify(): void
    {
        $this->client->request('POST', '/');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessRestore(): void
    {
        $this->client->request('GET', '/b');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('http://ya.ru/', $this->client->getResponse()->headers->get('location'));
    }

    public function testNotExistLinkRestore(): void
    {
        $this->client->request('GET', '/abc');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testExpiredLinkRestore(): void
    {
        $this->client->request('GET', '/c');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
