<?php
declare(strict_types=1);

namespace Oxford\Token;

class TBadCharacterTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'control-character' => [
                "<?php\n" . chr(1) . "\n",
                [
                    TPhpOpeningTag::class,
                    TBadCharacter::class,
                ],
            ],
        ];
    }
}
