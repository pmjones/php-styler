<?php
namespace PhpStyler\Command;

use AutoShell\Option;
use AutoShell\Options;

class PreviewOptions implements Options
{
    public function __construct(
        #[Option(
            'c,config',
            mode: Option::VALUE_REQUIRED,
            help: 'Path to the config file.',
        )]
        public readonly ?string $configFile,

        #[Option('debug-parser', help: "Dump parser nodes in output.")]
        public readonly ?bool $debugParser,

        #[Option('debug-printer', help: "Dump printables in output.")]
        public readonly ?bool $debugPrinter,

        #[Option('debug-styler', help: "Dump styler lines in output.")]
        public readonly ?bool $debugStyler,
    ) {
    }
}
