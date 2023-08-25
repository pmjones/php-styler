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

// why the argument split?
         return [
-            'type' => get_class($exception),
+            'type' => get_class(
+                $exception,
+            ),
             'code' => $code,
             'message' => $exception->getMessage(),
             'file' => $exception->getFile(),

```

