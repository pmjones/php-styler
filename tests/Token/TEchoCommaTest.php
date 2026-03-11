<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEchoCommaTest extends TTestCase
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
                echo $a, $b, $c;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TVariable::class,
                    TEchoComma::class,
                    TVariable::class,
                    TEchoComma::class,
                    TVariable::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}
