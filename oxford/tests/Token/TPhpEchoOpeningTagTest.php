<?php
declare(strict_types=1);

namespace Oxford\Token;

class TPhpEchoOpeningTagTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
