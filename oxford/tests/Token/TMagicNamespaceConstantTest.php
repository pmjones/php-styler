<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicNamespaceConstantTest extends TTestCase
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
                $foo = __NAMESPACE__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicNamespaceConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
