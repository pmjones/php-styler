```php

// heredoc does not indent the body properly. need to add the
// current indent to the start of each body line somehow.
 function foo()
 {
     if ($bar) {
         $baz = <<<BAZ
         more text
-                with $vars
-            and then the end
+        with $vars
+    and then the end

         BAZ;
     }
 }

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

```
