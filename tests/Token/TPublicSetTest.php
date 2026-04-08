<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PHPUnit\Framework\Attributes\RequiresPhp;

#[RequiresPhp('>=8.4')]
class TPublicSetTest extends TTestCase
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
                class Foo {
                    protected public(set) string $bar;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TProtected::class,
                    TPublicSet::class,
                    TString::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}
