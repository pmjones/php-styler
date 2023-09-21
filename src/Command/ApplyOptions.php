<?php
namespace PhpStyler\Command;

use AutoShell\Option;
use AutoShell\Options;

class ApplyOptions implements Options
{
    public function __construct(
        #[Option(
            'c,config',
            mode: Option::VALUE_REQUIRED,
            help: 'Path to the config file.',
        )]
        public readonly ?string $configFile,

        #[Option(
            'f,force',
            mode: Option::VALUE_REJECTED,
            help: 'Force styling regardless of cache.',
        )]
        public readonly ?string $force,
    ) {
    }
}
