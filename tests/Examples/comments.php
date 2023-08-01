<?php
/**
 * Namespace docblock
 */
namespace Whatever;

/**
 * Function dockblock
 */
function foo()
{
}

/**
 * Class docblock
 */
class Whatever
{
    // const
    public const FOO = 'foo';

    /**
     * Property docblock
     */
    public string $foo;

    // property
    public string $bar;

    /**
     * Method docblock
     */
    public function foo()
    {
        // first comment in method
        $i ++;

        // comment later in method
        $i ++;
    }

    // method
    public function bar()
    {
    }
}
