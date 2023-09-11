<?php
// normal concat
$foo = $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable;

// concat in argument
$foo = foo($veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable);

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

// no splits inside array elements
if (true) {
    if (true) {
        $foo = [
            'client' => 'required|numeric|model:' . Client::class,
            'location' => 'numeric|model:' . Location::class,
            'department' => 'numeric|model:' . Department::class,
        ];
    }
}
