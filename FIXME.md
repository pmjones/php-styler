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

return sprintf(
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


// something about ternaries?
$sourceDirs = explode('/', isset($basePath[0])
    && '/' === $basePath[0]
 ? substr($basePath, 1) : $basePath);

$sourceDirs = explode(
    '/',
    isset($basePath[0]) && '/' === $basePath[0]
        ? substr($basePath, 1)
        : $basePath
);

 // something about ternaries?
return ! isset($path[0]) || '/' === $path[0] || false !== (
    $colonPos = strpos($path, ':')
) && (
    $colonPos < (
        $slashPos = strpos($path, '/')
    ) || false === $slashPos
) ? "./{$path}" : $path;
```
