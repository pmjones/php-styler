<?php
declare(strict_types=1);

namespace Oxford\Token;

class TUseEndSemicolonTest extends TTestCase
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
                use Foo\Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
