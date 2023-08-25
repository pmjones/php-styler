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



// arrow in arg in array
function foo()
{
    return Payload::updated(
        ['source' => $this->veryLongMethodName(
            fn () : string => $this->anotherMethodName($source, $target),
        )],
    );
}
```
