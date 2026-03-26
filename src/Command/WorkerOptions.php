<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Option;
use AutoShell\Options;

class WorkerOptions implements Options
{
    public function __construct(
        #[Option(
            'c,config',
            mode: Option::VALUE_REQUIRED,
            help: 'Path to the config file.',
        )]
        public readonly ?string $configFile,

        #[Option(
            'm,mode',
            mode: Option::VALUE_REQUIRED,
            help: 'Worker mode: apply, check, or diff.',
        )]
        public readonly ?string $mode,
    ) {
    }
}
