<?php
declare(strict_types=1);

namespace Oxford\Token;

class TAnonymousClassTest extends TTestCase
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
                new class () {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'extends-string' => [
                <<<'CODE'
                <?php
                new class () extends Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TExtends::class,
                    TUnqualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'extends-name-qualified' => [
                <<<'CODE'
                <?php
                new class () extends Bar\Baz {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TExtends::class,
                    TQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'extends-name-fully-qualified' => [
                <<<'CODE'
                <?php
                new class () extends \Bar\Baz {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TExtends::class,
                    TFullyQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'implements-string' => [
                <<<'CODE'
                <?php
                new class () implements Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TImplements::class,
                    TUnqualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'implements-name-qualified' => [
                <<<'CODE'
                <?php
                new class () implements Bar\Baz {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TImplements::class,
                    TQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'implements-name-fully-qualified' => [
                <<<'CODE'
                <?php
                new class () implements \Bar\Baz {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TImplements::class,
                    TFullyQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'no-args' => [
                <<<'CODE'
                <?php
                new class {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'no-args-extends-string' => [
                <<<'CODE'
                <?php
                new class extends Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TExtends::class,
                    TUnqualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'no-args-implements-string' => [
                <<<'CODE'
                <?php
                new class implements Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TImplements::class,
                    TUnqualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
