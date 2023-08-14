```php
// splitting long closure parameters and uses
$veryLongVariableName = function ($veryLongVar1, $veryLongVar2) use ($veryLongVar3,
$veryLongVar4) {
    // function body
};

// indenting after closure as argument
class foo
{
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $psr7Request = $this
            ->populateRequestParametersFromRoute(
                $this->loadRequest()->withAttribute(RouteMatch::class, $routeMatch),
                $routeMatch,
            );
        $result = $this
            ->pipe
            ->process(
                $psr7Request,
                new CallableDelegateDecorator(static function () : void {
                    throw ReachedFinalHandlerException::create();
                }, $this->responsePrototype));
                    $e->setResult($result);
                    return $result;
                }
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

// splits member instead of argument, when argument
// might make more sense
-            $html .= $this->radio(
-                $name,
-                $value,
-                (string) $optionValue,
-                (string) $optionLabel,
-                $attr,
-                $__attr
-            );
+            $html .= $this
+                ->radio(
+                    $name,
+                    $value,
+                    (string) $optionValue,
+                    (string) $optionLabel,
+                    $attr,
+                    $__attr,
+                );
```
