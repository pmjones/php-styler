```php
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
// would prefer splits at booleans then ternary.
return ! isset($path[0]) || '/' === $path[0] || false !== (
    $colonPos = strpos($path, ':')
) && (
    $colonPos < (
        $slashPos = strpos($path, '/')
    ) || false === $slashPos
) ? "./{$path}" : $path;
```
