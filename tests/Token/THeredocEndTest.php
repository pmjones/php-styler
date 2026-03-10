<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class THeredocEndTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'end-heredoc' => [
                "<?php\n\$foo = <<<EOT\nhello\nEOT;\n",
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
