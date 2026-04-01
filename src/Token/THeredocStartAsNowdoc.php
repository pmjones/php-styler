<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class THeredocStartAsNowdoc extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // already a nowdoc? (label is single-quoted)
        if (str_contains($source->text, "'")) {
            $parser->addNesting($source, THeredocStart::class);
            return;
        }

        // scan forward in source tokens for interpolation
        $i = $parser->getSourceOffset() + 1;

        while (true) {
            $peek = $parser->getSourceAt($i);

            if ($peek->is(T_END_HEREDOC)) {
                break;
            }

            if ($peek->is(T_VARIABLE) || $peek->is(T_CURLY_OPEN)) {
                // has interpolation, cannot convert
                $parser->addNesting($source, THeredocStart::class);
                return;
            }

            $i ++;
        }

        // convert <<<LABEL\n to <<<'LABEL'\n
        if (preg_match('/^(<<<\s*)(\w+)(\s*)$/', $source->text, $matches)) {
            $newText = $matches[1] . "'" . $matches[2] . "'" . $matches[3];

            $source = new PhpToken(
                T_START_HEREDOC,
                $newText,
                $source->line,
                $source->pos,
            );
        }

        $parser->addNesting($source, THeredocStart::class);
    }
}
