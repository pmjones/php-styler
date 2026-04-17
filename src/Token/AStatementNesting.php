<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Marker for statement-level nesting that should be popped
 * when a semicolon closes an inner expression (e.g., ternary).
 */
interface AStatementNesting
{
}
