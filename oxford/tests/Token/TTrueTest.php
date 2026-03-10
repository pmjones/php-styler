<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TTrueTest extends TTestCase
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
                $foo = true;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TTrue::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
