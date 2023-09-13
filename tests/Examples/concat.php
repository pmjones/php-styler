<?php
// normal concat
$foo = $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable
    . $veryVeryLongVariable;

// concat in argument
$foo = foo(
    $veryVeryLongVariable
        . $veryVeryLongVariable
        . $veryVeryLongVariable
        . $veryVeryLongVariable,
);

// function call after concat
$something = $veryLongVariable
    . sprintf(
        '%s %s %s',
        $this->getMethod(),
        $this->getRequestUri(),
        $this->server->get('SERVER_PROTOCOL'),
    );

function concat_after_function()
{
    // concat after function call looks off
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

    // fix by extracting the function call
    $statusLine = sprintf(
        '%s %s %s',
        $this->getMethod(),
        $this->getRequestUri(),
        $this->server->get('SERVER_PROTOCOL'),
    );
    return $statusLine . "\r\n" . $this->headers . $cookieHeader . "\r\n" . $content;
}

if (true) {
    if (true) {
        $foo = [
            'client' => 'required|numeric|model:' . Client::class,
            'location' => 'numeric|model:' . Location::class,
            'department' => 'numeric|model:' . Department::class,
        ];
    }
}
