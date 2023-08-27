```php
// COMMENTING OUT THE SWITCH BODY DELETES THE BODY (IE ALL THE COMMENTS).
// PARSER DOES NOT EVEN SEE THOSE COMMENTS.
switch ($foo) {
    // this comment will be completely removed
}

// losing indent -- it is because of the over-long array-dim-fetch line.
// you can fix the line, but really, the outdenting should not happen merely
// because of an over-long line.
+            $data = array_reduce(
+                $result['docs'],
+                function (
+                    $data,
+                    $row,
+                ) {
+                    $data[$row['_id']] = $row['_source']['source']['data']['businessUnit']['name'];
+            return $data;
+        },

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

// fluency not quite working with a static that gets split
+            return self::fromResponse(
+                $response,
+                $overrideStatus ?? DomainStatus::UNAUTHORIZED,
+            )
+                ->setException(
+                    $e,
+                );


// should an array as single argument be clipped into the parens?
// note that the trailing comma should go away.
+            return Payload::notFound(
+                [
+                    'query' => $this->dataProvider->getQuery(),
+                    'startDate' => $this->dataProvider->getStartDate(),
+                ],
+            );

// should single-arg calls even be split at all?
// this is where having array split earlier is better
-        $credentials = [
-            $this->config->get('RAGSDALE_DOMO_CLIENT_ID'),
-            $this->config->get('RAGSDALE_DOMO_CLIENT_SECRET'),
-        ];
+        $credentials = [$this->config->get(
+            'RAGSDALE_DOMO_CLIENT_ID',
+        ), $this->config->get(
+            'RAGSDALE_DOMO_CLIENT_SECRET',
+        )];

// splits weird
-                return is_array($item)
-                    ? [...$carry, ...self::flattenDeep($item)]
-                    : [...$carry, $item];
+                return is_array(
+                    $item,
+                ) ? [...$carry, ...self::flattenDeep(
+                    $item,
+                )] : [...$carry, $item];

// splits weird
         return true === array_reduce(
             $values,
-            fn ($result, $value) => $result && $value,
-            true
+            fn ($result, $value) => $result
+                && $value,
+            true,
         );
     }
```

