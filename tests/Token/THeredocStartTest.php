<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class THeredocStartTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'heredoc' => [
                <<<'CODE'
                <?php
                $foo = <<<EOT
                bar
                EOT;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    THeredocStart::class,
                    TStringFragment::class,
                    THeredocEnd::class,
                    TSemicolon::class,
                ],
            ],
            'nowdoc' => [
                <<<'CODE'
                <?php
                $foo = <<<'EOT'
                bar
                EOT;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    THeredocStart::class,
                    TStringFragment::class,
                    THeredocEnd::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
