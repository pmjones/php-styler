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
