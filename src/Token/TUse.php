<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_USE
 *
 * Syntax: use
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces
 */
class TUse extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TClasslikeOpeningBrace::class)) {
            $parser->parse($source, TUseTrait::class);
            return;
        }

        // expand grouped imports (use Foo\{Bar, Baz}) at source level
        self::expandGroupedImport($parser);

        $parser->addNesting($source, self::class);
    }

    private static function expandGroupedImport(Parser $parser) : void
    {
        $sourceCount = $parser->getSourceCount();
        $sourceOffset = $parser->getSourceOffset();

        // scan from after `use` to find `{` before `;`
        $keyword = null;
        $prefix = '';
        $braceOffset = null;

        for ($i = $sourceOffset + 1; $i < $sourceCount; $i ++) {
            $token = $parser->getSourceAt($i);

            if ($token->is('{')) {
                $braceOffset = $i;
                break;
            }

            if ($token->is(';')) {
                return; // not a grouped import
            }

            if ($token->is(T_FUNCTION)) {
                $keyword = T_FUNCTION;
            } elseif ($token->is(T_CONST)) {
                $keyword = T_CONST;
            } elseif ($token->is([T_STRING, T_NAME_QUALIFIED])) {
                if ($prefix !== '') {
                    $prefix .= '\\';
                }

                $prefix .= $token->text;
            }
        }

        if ($braceOffset === null) {
            return;
        }

        // find closing brace
        $closeBraceOffset = null;

        for ($i = $braceOffset + 1; $i < $sourceCount; $i ++) {
            if ($parser->getSourceAt($i)->is('}')) {
                $closeBraceOffset = $i;
                break;
            }
        }

        if ($closeBraceOffset === null) {
            return;
        }

        // find semicolon after closing brace
        $semicolonOffset = null;

        for ($i = $closeBraceOffset + 1; $i < $sourceCount; $i ++) {
            $token = $parser->getSourceAt($i);

            if ($token->is(';')) {
                $semicolonOffset = $i;
                break;
            }

            if (! $token->is(T_WHITESPACE)) {
                break;
            }
        }

        if ($semicolonOffset === null) {
            return;
        }

        // collect segments from inside braces
        $segments = [];
        $name = '';
        $alias = null;
        $segKeyword = null;
        $inAs = false;

        for ($i = $braceOffset + 1; $i < $closeBraceOffset; $i ++) {
            $token = $parser->getSourceAt($i);

            if ($token->is(',')) {
                if ($name !== '') {
                    $segments[] = [
                        'keyword' => $segKeyword,
                        'name' => $name,
                        'alias' => $alias,
                    ];
                }

                $name = '';
                $alias = null;
                $segKeyword = null;
                $inAs = false;
                continue;
            }

            if ($token->is(T_WHITESPACE)) {
                continue;
            }

            if ($token->is(T_AS)) {
                $inAs = true;
                continue;
            }

            if ($token->is(T_FUNCTION)) {
                $segKeyword = T_FUNCTION;
                continue;
            }

            if ($token->is(T_CONST)) {
                $segKeyword = T_CONST;
                continue;
            }

            if ($inAs && $token->is(T_STRING)) {
                $alias = $token->text;
                continue;
            }

            if ($token->is([T_STRING, T_NAME_QUALIFIED])) {
                $name = $token->text;
            }
        }

        if ($name !== '') {
            $segments[] = [
                'keyword' => $segKeyword,
                'name' => $name,
                'alias' => $alias,
            ];
        }

        if ($segments === []) {
            return;
        }

        // build replacement tokens
        $replacement = [];

        foreach ($segments as $idx => $segment) {
            if ($idx > 0) {
                $replacement[] = new PhpToken(T_WHITESPACE, "\n");
                $replacement[] = new PhpToken(T_USE, 'use');
            }

            $replacement[] = new PhpToken(T_WHITESPACE, ' ');

            // per-segment keyword (from inside braces) takes precedence
            $effectiveKeyword = $segment['keyword'] ?? $keyword;

            if ($effectiveKeyword === T_FUNCTION) {
                $replacement[] = new PhpToken(T_FUNCTION, 'function');
                $replacement[] = new PhpToken(T_WHITESPACE, ' ');
            } elseif ($effectiveKeyword === T_CONST) {
                $replacement[] = new PhpToken(T_CONST, 'const');
                $replacement[] = new PhpToken(T_WHITESPACE, ' ');
            }

            $fullName = $prefix . '\\' . $segment['name'];
            $replacement[] = new PhpToken(T_NAME_QUALIFIED, $fullName);

            if ($segment['alias'] !== null) {
                $replacement[] = new PhpToken(T_WHITESPACE, ' ');
                $replacement[] = new PhpToken(T_AS, 'as');
                $replacement[] = new PhpToken(T_WHITESPACE, ' ');
                $replacement[] = new PhpToken(T_STRING, $segment['alias']);
            }

            $replacement[] = new PhpToken(ord(';'), ';');
        }

        // splice: replace everything from sourceOffset+1 through semicolonOffset
        $parser->spliceSource(
            $sourceOffset + 1,
            $semicolonOffset - $sourceOffset,
            $replacement,
        );
    }
}
