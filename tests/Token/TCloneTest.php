<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCloneTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'clone' => [
                <<<'CODE'
                <?php
                $foo = clone $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TClone::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
