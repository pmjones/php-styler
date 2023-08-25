<?php
// normal concat
$foo = $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable;

// no splits in arguments
$foo = foo(
    $veryVeryLongVariable . $veryVeryLongVariable . $veryVeryLongVariable . $veryVeryLongVariable,
);

// function call after concat looks good ...
$something = $veryLongVariable
    . sprintf(
        '%s %s %s',
        $this->getMethod(),
        $this->getRequestUri(),
        $this->server->get('SERVER_PROTOCOL'),
    );

// ... but concat after function call looks bad:
function bad_concat()
{
    $message = sprintf(
        '%s %s %s',
        $this->getMethod(),
        $this->getRequestUri(),
        $this->server->get('SERVER_PROTOCOL'),
    )
        . "\r\n"
        . $this->headers
        . $cookieHeader
        . "\r\n"
        . $content;
}

// add a placeholder empty string to help ...
function fix_concat_1()
{
    $statusLine = ''
        . sprintf(
            '%s %s %s',
            $this->getMethod(),
            $this->getRequestUri(),
            $this->server->get('SERVER_PROTOCOL'),
        )
        . "\r\n"
        . $this->headers
        . $cookieHeader
        . "\r\n"
        . $content;
}

// ... or split out the function call.
function fix_concat_2()
{
    $statusLine = sprintf(
        '%s %s %s',
        $this->getMethod(),
        $this->getRequestUri(),
        $this->server->get('SERVER_PROTOCOL'),
    );
    return $statusLine . "\r\n" . $this->headers . $cookieHeader . "\r\n" . $content;
}
