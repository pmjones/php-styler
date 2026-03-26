<?php
namespace PhpStyler\Command;

use AutoShell\Option;
use AutoShell\Options;

class CheckOptions implements Options
{
    public function __construct(
        #[Option(
            'c,config',
            mode: Option::VALUE_REQUIRED,
            help: 'Path to the config file.',
        )]
        public readonly ?string $configFile,

        #[Option(
            'w,workers',
            mode: Option::VALUE_REQUIRED,
            help: 'Number of parallel workers (default 1; use "auto" for CPU count).',
        )]
        public readonly ?string $workers,
    ) {
    }
}
