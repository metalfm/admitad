<?php declare(strict_types=1);

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Config;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/** @noinspection AutoloadingIssuesInspection */

class MoveStrictTypeFixer extends AbstractFixer
{
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        $seq = $tokens->findSequence([
            new Token([T_OPEN_TAG, "<?php\n"]),
            new Token([T_DECLARE, 'declare']),
        ]);

        if (null === $seq) {
            return;
        }

        $keys = array_keys($seq);
        foreach (array_fill($keys[0], max($keys), null) as $key => $value) {
            $token = $tokens[$key];
            if ($token->isGivenKind(T_OPEN_TAG)) {
                $tokens[$key] = new Token([$token->getId(), str_replace("\n", ' ', $token->getContent())]);
                continue;
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($key);
        }
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'Переместить declare(strict_types=1); на одну строку с <?php',
            [
                new CodeSample(
                    '<?php declare(strict_types=1);'
                ),
            ]
        );
    }

    public function getName(): string
    {
        return sprintf('MyFixer/%s', parent::getName());
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return PHP_VERSION_ID >= 70000 && $tokens[0]->isGivenKind(T_OPEN_TAG);
    }
}

return Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@DoctrineAnnotation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'cast_spaces' => ['space' => 'none'],
        'array_indentation' => true,
        'native_function_invocation' => true,
        'MyFixer/move_strict_type' => true,
        'single_class_element_per_statement' => false,
        'no_superfluous_phpdoc_tags' => false,
        'function_declaration' => ['closure_function_spacing' => 'none']
    ])
    ->setCacheFile(__DIR__.'/var/.php_cs.cache')
    ->registerCustomFixers([
        new MoveStrictTypeFixer(),
    ]);
