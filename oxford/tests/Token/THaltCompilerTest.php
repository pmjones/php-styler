<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class THaltCompilerTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                __halt_compiler();
                CODE,
                [
                    TPhpOpeningTag::class,
                    THaltCompiler::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    THaltCompilerSemicolon::class,
                ],
            ],
        ];
    }
}
