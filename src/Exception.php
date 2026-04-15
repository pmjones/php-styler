<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpToken;

class Exception extends \Exception
{
    public static function fromParser(
        string $message,
        Parser $parser,
        PhpToken $source,
    ) : self
    {
        $recentSourceText = '';
        $offset = $parser->getSourceOffset();

        for ($i = max(0, $offset - 10); $i < $offset; $i ++) {
            $recentSourceText .= $parser->getSourceAt($i)->text;
        }

        $upcomingSourceText = '';
        $count = $parser->getSourceCount();

        for ($i = $offset + 1; $i < min($count, $offset + 6); $i ++) {
            $upcomingSourceText .= $parser->getSourceAt($i)->text;
        }

        return new self(
            message: $message,
            debug: [
                'line' => $source->line,
                'pos' => $source->pos,
                'currentTokenText' => $source->text,
                'currentTokenName' => $source->getTokenName(),
                'recentSourceText' => $recentSourceText,
                'upcomingSourceText' => $upcomingSourceText,
            ],
        );
    }

    /** @var array<string, mixed> */
    public readonly array $debug;

    /**
     * @param array<string, mixed> $debug
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        array $debug = [],
    ) {
        parent::__construct($message, $code, $previous);
        $this->debug = $debug;
    }
}
