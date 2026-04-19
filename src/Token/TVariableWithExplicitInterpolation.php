<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TVariableWithExplicitInterpolation extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! $parser->inEncapsedString()) {
            TVariable::parse($parser, $source);
            return;
        }

        // Already wrapped in curlies?
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TCurlyOpen) {
            $parser->add($source, TEncapsedVariable::class);
            return;
        }

        // Determine the end of the access chain in source tokens
        $i = $parser->source->offset();
        $count = $parser->source->count();
        $endOffset = $i; // default: just the variable

        $next = ($i + 1 < $count) ? $parser->source->getAt($i + 1) : null;

        if ($next !== null && $next->text === '[') {
            // Array access: $foo[key] — find closing ]
            $j = $i + 2;

            while ($j < $count && $parser->source->getAt($j)->text !== ']') {
                $j ++;
            }

            if ($j < $count) {
                $endOffset = $j;
            }
        } elseif (
            $next !== null
            && (
                $next->is(T_OBJECT_OPERATOR)
                || $next->is(T_NULLSAFE_OBJECT_OPERATOR)
            )
        ) {
            // Property access: $foo->bar
            $endOffset = $i + 1; // the operator

            if ($i + 2 < $count && $parser->source->getAt($i + 2)->is(T_STRING)) {
                $endOffset = $i + 2; // the property name
            }
        }

        // Insert { before the variable and } after the access chain.
        // Splice } first (at higher offset) so it doesn't shift { position.
        $openToken = new PhpToken(T_CURLY_OPEN, '{', $source->line, $source->pos);
        $endSource = $parser->source->getAt($endOffset);

        $closeToken = new PhpToken(
            ord('}'),
            '}',
            $endSource->line,
            $endSource->pos,
        );

        $parser->source->splice($endOffset + 1, 0, [$closeToken]);
        $parser->source->splice($i, 0, [$openToken]);

        // Back up so the main loop processes { on the next iteration
        $parser->source->setOffset($i - 1);
    }
}
