```php
// COMMENTING OUT THE SWITCH BODY DELETES THE BODY (IE ALL THE COMMENTS).
// PARSER DOES NOT EVEN SEE THOSE COMMENTS.
switch ($foo) {
    // this comment will be completely removed
}

// comment vs closure signature
$this->htmlAttrMatcher =

/** @param array<array-key, string> $matches */
function (array $matches) : string {
   return $this->htmlAttrMatcher($matches);
};
$this->jsMatcher =

/** @param array<array-key, string> $matches */
function (array $matches) : string {
   return $this->jsMatcher($matches);
};
$this->cssMatcher =

/** @param array<array-key, string> $matches */
function (array $matches) : string {
   return $this->cssMatcher($matches);
};

// splits at args "too soon".
// should double-indent args
// to match with concats.
return sprintf('%s %s %s',
    $this->getMethod(),
    $this->getRequestUri(),
    $this->server->get('SERVER_PROTOCOL'),
)
    . "\r\n"
    . $this->headers
    . $cookieHeader
    . "\r\n"
    . $content;


// splits at boolean "too soon".
// would prefer split at args.
$sourceDirs = explode('/', isset($basePath[0])
    && '/' === $basePath[0]
 ? substr($basePath, 1) : $basePath);

$sourceDirs = explode(
    '/',
    isset($basePath[0]) && '/' === $basePath[0]
        ? substr($basePath, 1)
        : $basePath
);

// splits at precedence "too soon".
// would prefer splits at boolean, then at ternary.
return ! isset($path[0]) || '/' === $path[0] || false !== (
    $colonPos = strpos($path, ':')
) && (
    $colonPos < (
        $slashPos = strpos($path, '/')
    ) || false === $slashPos
) ? "./{$path}" : $path;

// coalesce line split here messes up indents.
function foo_broke()
{
    $maxlifetime = (int) (($this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl)
        ?? \ini_get('session.gc_maxlifetime')
);
}

// fix by moving a bit of code around.
function foo_fixed()
{
    $ttl = $this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl;
    $maxlifetime = (int) ($ttl ?? \ini_get('session.gc_maxlifetime'));
}
```
