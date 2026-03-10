<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseConstTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'global' => [
                <<<'CODE'
                <?php
                use const FOO;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TConstName::class,
                    TUseEndSemicolon::class,
                ]
            ],
            'namespaced' => [
                <<<'CODE'
                <?php
                use const Foo\BAR;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ]
            ],
            'string-as' => [
                <<<'CODE'
                <?php
                use const FOO as BAR;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TConstName::class,
                    TUseAs::class,
                    TConstName::class,
                    TUseEndSemicolon::class,
                ]
            ],
            'namespaced-as' => [
                <<<'CODE'
                <?php
                use const Foo\BAR as BAZ;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TQualifiedName::class,
                    TUseAs::class,
                    TConstName::class,
                    TUseEndSemicolon::class,
                ]
            ],
            'grouped' => [
                <<<'CODE'
                <?php
                use const FOO, BAR as BAZ, DIB;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TConstName::class,
                    TUseComma::class,
                    TConstName::class,
                    TUseAs::class,
                    TConstName::class,
                    TUseComma::class,
                    TConstName::class,
                    TUseEndSemicolon::class,
                ]
            ],
            'prefixed' => [
                <<<'CODE'
                <?php
                use const Foo\Bar\{BAZ, DIB as ZIM, GIR};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TQualifiedName::class,
                    TNamespaceSeparator::class,
                    TUseConstOpeningBrace::class,
                    TConstName::class,
                    TUseComma::class,
                    TConstName::class,
                    TUseAs::class,
                    TConstName::class,
                    TUseComma::class,
                    TConstName::class,
                    TUseConstClosingBrace::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
