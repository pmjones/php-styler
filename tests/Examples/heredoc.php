<?php
$foo = <<<END
more text
        with {$vars}
    and then the end
END;
function foo()
{
    if ($bar) {
        $foo = <<<END
        more text
                with {$this->vars}
            and then the end
        END;
    }
}
