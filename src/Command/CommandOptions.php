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
        )]
        public readonly ?string $configFile,
    ) {
    }
}
