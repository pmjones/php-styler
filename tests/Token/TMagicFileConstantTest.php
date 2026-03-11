<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicFileConstantTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'magic-constant' => [
                <<<'CODE'
                <?php
                $foo = __FILE__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicFileConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
