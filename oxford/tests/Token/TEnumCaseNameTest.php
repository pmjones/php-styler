<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEnumCaseNameTest extends TTestCase
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
                enum Color {
                    case Red;
                    case Blue;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumOpeningBrace::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
            'backed' => [
                <<<'CODE'
                <?php
                enum Suit : string {
                    case Hearts = 'hearts';
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumBackedColon::class,
                    TString::class,
                    TEnumOpeningBrace::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
