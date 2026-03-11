<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBooleanOrTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'boolean-or' => [
                <<<'CODE'
                <?php
                $foo = true || false;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TTrue::class,
                    TBooleanOr::class,
                    TFalse::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
