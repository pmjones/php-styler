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


// should do instance_op before args?
-            return Payload::fromThrowable($e, DomainStatus::INVALID)
-                ->setError(self::ERROR_ALREADY_CONNECTED);
+            return Payload::fromThrowable(
+                $e,
+                DomainStatus::INVALID,
+            )->setError(
+                self::ERROR_ALREADY_CONNECTED,
+            );


// something about the arry with the arrow function
-                return Payload::updated([
-                    'source' => $this->executeInTransaction(
-                        fn (): FieldedgeAutomationSettings => $this->refresh($source, $credentials)
-                    )
-                ]);
+                return Payload::updated(
+                    ['source' => $this->executeInTransaction(
+                        fn (): FieldedgeAutomationSettings => $this->refresh(
+                            $source,
+                            $credentials,
+                        ),
+                    )],
+                );
```
