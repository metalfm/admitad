<?php declare(strict_types=1);

namespace App\Shortener;

class Alphabet
{
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private int $length;

    public function __construct()
    {
        $this->length = \strlen(self::ALPHABET);
    }

    public function encode(int $i): string
    {
        if (0 === $i) {
            return self::ALPHABET[0];
        }

        $buffer = '';
        while ($i > 0) {
            $buffer .= self::ALPHABET[$i % $this->length];
            $i = (int)($i / $this->length);
        }

        return \strrev($buffer);
    }

    public function decode(string $string): int
    {
        $num = 0;
        $length = \strlen($string);
        $alphabetHash = \array_flip(\str_split(self::ALPHABET));

        for ($i = 0; $i < $length; ++$i) {
            $num = ($num * $this->length) + $alphabetHash[$string[$i]];
        }

        return $num;
    }
}
