<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStringCastTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                $foo = (string) $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TStringCast::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
