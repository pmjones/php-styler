<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIsNotIdenticalTest extends TTestCase
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
                $foo !== $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TIsNotIdentical::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
