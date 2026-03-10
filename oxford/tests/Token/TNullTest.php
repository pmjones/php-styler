<?php
declare(strict_types=1);

namespace Oxford\Token;

class TNullTest extends TTestCase
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
                $foo = null;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TNull::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
