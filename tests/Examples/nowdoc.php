<?php
$foo = <<<'END'
more text
        with $vars
    and then the end
END;

function foo()
{
    if ($bar) {
        $baz = <<<'BAZ'
        more text
                with $vars
            and then the end
        BAZ;
    }
}

function bar()
{
    $foo = 'bar';

    $query = <<<'SQL'
        SELECT
            *
        FROM
            table
        WHERE
            foo = bar
    SQL;

    return new Bar($query);
}

$baz = dib(
    <<<'SQL'
        SELECT
            *
        FROM
            table
        WHERE
            foo = bar
    SQL,
    'bar',
    'baz',
);

$foo = [
    <<<'SQL'
        SELECT
            *
        FROM
            table
        WHERE
            foo = bar
    SQL,
    'bar',
    'baz',
];

#[Help(
    <<<'HELP'
        Apply styling to these space-separated files and directories;
        overrides the files specified in config.
    HELP,
)]
function zim()
{
}
