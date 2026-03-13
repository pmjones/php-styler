<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBadCharacterTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'control-character' => [
                "<?php\n"
                    . chr(
                        1,
                    )
                    . "\n",
                [
                    TPhpOpeningTag::class,
                    TBadCharacter::class,
                ],
            ],
        ];
    }
}
