<?php declare(strict_types=1);

namespace App\Tests\Shortener;

use App\Shortener\Alphabet;
use PHPUnit\Framework\TestCase;

class AlphabetTest extends TestCase
{
    private Alphabet $a;

    protected function setUp(): void
    {
        $this->a = new Alphabet();
    }

    public function testEncode(): void
    {
        $this->assertEquals('a', $this->a->encode(0));
        $this->assertEquals('hello', $this->a->encode(104430644));
    }

    public function testDecode(): void
    {
        $this->assertEquals(0, $this->a->decode(''));
        $this->assertEquals(104430644, $this->a->decode('hello'));
    }
}
