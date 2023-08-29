```php
// COMMENTING OUT THE SWITCH BODY DELETES THE BODY (IE ALL THE COMMENTS).
// PARSER DOES NOT EVEN SEE THOSE COMMENTS.
switch ($foo) {
    // this comment will be completely removed
}

// loses indent because of line-too-long (array dim fetch).
// you can fix the line, but really, the outdenting should not happen merely
// because of an over-long line. alternatively, split array-dim-fetch.
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

// fluency not quite working with a static that gets split -- may be a line-length issue
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

// splits weird inside closure body -- is OK when by itself
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

// array should nestle -- is only arg
-                return Payload::updated([
-                    'source' => $this->executeInTransaction(
-                        fn (): FooBarBazAutomationSettings => $this->refresh($source, $credentials)
-                    )
-                ]);
+                return Payload::updated(
+                    [
+                        'source' => $this->executeInTransaction(
+                            fn (): FooBarBazAutomationSettings => $this->refresh(
+                                $source,
+                                $credentials,
+                            ),
+                        ),
+                    ],
+                );

// treat concat as "expansive" ?
             if (
-                false == $replacement
-                || ! ($controller === $namespace || str_starts_with($controller, $namespace . '\\'))
+                false == $replacement || ! (
+                    $controller === $namespace || str_starts_with($controller, $namespace
+                        . '\\'
+                    )
+                )
             ) {
                 continue;
             }

// expanding method args too soon?

+            && \in_array(
+                strtoupper($request->server->get('REQUEST_METHOD',
+                'GET')),
+                ['PUT', 'DELETE', 'PATCH'],
+            )

// breaks inside closures but not by itself

-                return is_array($item)
-                    ? [...$carry, ...self::flattenDeep($item)]
-                    : [...$carry, $item];
+                return is_array($item) ? [
+                    ...$carry,
+                    ...self::flattenDeep($item),
+                ] : [
+                    ...$carry,
+                    $item,
+                ];


// weird fluent-new split

-        $query = (string) (new Query($resource))
-            ->setVariables([new Variable('offset', 'Int')])
-            ->setArguments(['offset' => '$offset'])
-            ->setSelectionSet([(new Query($resource))->setSelectionSet($fields)]);
-
-        $result = $this->httpClient->post($this->endpointUrl, [
-            'json' => [
-                'query' => $query,
-                'variables' => compact('offset')
-            ]
+        $query = (string) (new Query($resource))->setVariables([
+            new Variable('offset', 'Int'),
+        ])->setArguments([
+            'offset' => '$offset',
+        ])->setSelectionSet([
+            (new Query($resource))->setSelectionSet($fields),
         ]);
-
+        $result = $this->httpClient->post(
+            $this->endpointUrl,
+            ['json' => ['query' => $query, 'variables' => compact('offset')]],
+        );

// split arrow functions at arrow?

-        return fn (ResponseInterface $response): array =>
-            json_decode((string) $response->getBody(), true);
+        return fn (ResponseInterface $response): array => json_decode(
+            (string) $response->getBody(),
+            true,
+        );

// ???

         $note = (string) $this->templateViewFactory->new(
-            $this->path->templates() . 'email/automation/deactivation_notice.txt',
+            $this->path->templates()
+                . 'email/automation/deactivation_notice.txt',
             [
                 'integration' => $source->name,
                 'status' => $status,
                 'user' => $user,
                 'sudoer' => $sudoer,
                 'client' => $client,
-                'clientUrl' => sprintf(
-                    'https://%s/crm/%d/profile',
-                    $this->config->get('DOMAIN_OFFICE'),
-                    $client->pk()
-                ),
-            ]
+                'clientUrl' => sprintf('https://%s/crm/%d/profile',
+                $this->config->get('DOMAIN_OFFICE'),
+                $client->pk()),
+            ],
         );

// split on pipes?

-        $this->enabled = $this->enabled
-            | self::FOOBAR_BUSINESS_UNITS
-            | self::FOOBAR_CAMPAIGNS
-            | self::FOOBAR_JOB_TYPES
-            | self::FOOBAR_CUSTOMERS
-            | self::FOOBAR_MEMBERSHIPS
-            | self::FOOBAR_MEMBERSHIP_TYPES
-            | self::FOOBAR_INVOICES
-            | self::FOOBAR_TAGS
-            | self::FOOBAR_APPOINTMENTS
-            | self::FOOBAR_ESTIMATES
-            | self::FOOBAR_CUSTOMER_CONTACTS
-            | self::FOOBAR_TECHNICIANS;
-
+        $this->enabled = $this->enabled | self::FOOBAR_BUSINESS_UNITS | self::FOOBAR_CAMPAIGNS | self::FOOBAR_JOB_TYPES | self::FOOBAR_CUSTOMERS | self::FOOBAR_MEMBERSHIPS | self::FOOBAR_MEMBERSHIP_TYPES | self::FOOBAR_INVOICES | self::FOOBAR_TAGS | self::FOOBAR_APPOINTMENTS | self::FOOBAR_ESTIMATES | self::FOOBAR_CUSTOMER_CONTACTS | self::FOOBAR_TECHNICIANS;


// ???
         return array_map(
-            fn (string $entry): BaseWebhook => $webhookClass::new(
-                json_decode($entry, true, 512, JSON_THROW_ON_ERROR)
-            ),
-            $samples
+            fn (string $entry): BaseWebhook => $webhookClass::new(json_decode($entry,
+            true,
+            512,
+            JSON_THROW_ON_ERROR)),
+            $samples,
         );

// split coalesce on ??= as well?
-        $this->vars['HORIZON_DOMO_CLIENT_ID'] ??= $this->vars['DOMO_SANDBOX_CLIENT_ID'] ?? null;
+        $this->vars['HORIZON_DOMO_CLIENT_ID'] ??= $this->vars['DOMO_SANDBOX_CLIENT_ID']
+            ?? null;

```
