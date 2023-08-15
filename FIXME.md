```php
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


// do not break members (or array dim fetches) on left side of assignment


        return sprintf(
            '%s %s %s',
            $this->getMethod(),
            $this->getRequestUri(),
            $this->server->get('SERVER_PROTOCOL'),
        )
            . "\r\n"
            . $this
                ->headers
            . $cookieHeader
            . "\r\n"
            . $content;


        // if args have closure, force-split the args
        self::$trustedProxies = array_reduce($proxies, function ($proxies, $proxy) {
            if ('REMOTE_ADDR' !== $proxy) {
                $proxies[] = $proxy;
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $proxies[] = $_SERVER['REMOTE_ADDR'];
            }

            return $proxies;
        }, []);


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
