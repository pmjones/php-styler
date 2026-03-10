<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TExitTest extends TTestCase
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
                exit();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TExit::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'empty' => [
                <<<'CODE'
                <?php
                exit;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TExit::class,
                    TSemicolon::class,
                ],
            ],
            'die' => [
                <<<'CODE'
                <?php
                die();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TExit::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'die-empty' => [
                <<<'CODE'
                <?php
                die;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TExit::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
