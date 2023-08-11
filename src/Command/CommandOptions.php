<?php
namespace PhpStyler\Command;

use AutoShell\Option;
use AutoShell\Options;

class CommandOptions implements Options
{
    public function __construct(
        #[Option(
            'c,config',
            mode: Option::VALUE_REQUIRED,
            help: 'Path to the config file.'
        )]
        public readonly ?string $configFile,
        #[Option(
            'f,force',
            help: "Force styling regardless of cache."
        )]
        public readonly ?bool $force,
    ) {
    }
}
