<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPhpEchoOpeningTagTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                '<?= $foo ?>',
                [
                    TPhpEchoOpeningTag::class,
                    TVariable::class,
                    TPhpClosingTag::class,
                ],
            ],
        ];
    }
}
