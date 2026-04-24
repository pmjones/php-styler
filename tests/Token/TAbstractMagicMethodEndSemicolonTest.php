<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAbstractMagicMethodEndSemicolonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'abstract-magic-with-return-type' => [
                <<<'CODE'
                <?php
                abstract class Foo {
                    abstract public function __toString() : string;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAbstract::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TAbstract::class,
                    TPublic::class,
                    TMagicMethod::class,
                    TMagicMethodName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TString::class,
                    TAbstractMagicMethodEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}
