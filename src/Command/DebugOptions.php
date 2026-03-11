<?php
namespace PhpStyler\Command;

use AutoShell\Option;
use AutoShell\Options;

class DebugOptions implements Options
{
    public function __construct(
        #[Option(
            'c,config',
            mode: Option::VALUE_REQUIRED,
            help: 'Path to the config file.',
        )]
        public readonly ?string $configFile,

        #[Option('parse', help: "Dump parsed tokens.")]
        public readonly ?bool $parse,

        #[Option('assemble', help: "Dump assembled lines of tokens.")]
        public readonly ?bool $assemble,

        #[Option('split', help: "Dump split lines.")]
        public readonly ?bool $split,
    ) {
    }
}
