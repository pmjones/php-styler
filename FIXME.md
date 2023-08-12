```php
// from Nette
-               } catch (\Throwable $e) {
+               } catch (Throwable $e) {

// from slim -- ???
 final class CallableResolver implements AdvancedCallableResolverInterface
 {
-    public static string $callablePattern = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';
+    public static string $callablePattern = '!^([^\\:]+)\\:([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)$!';

-    private ?ContainerInterface $container;
+private ?ContainerInterface $container;

// also from slime -- something about string literals and semicolons?
 class HttpSoftPsr17Factory extends Psr17Factory
 {
-    protected static string $responseFactoryClass = 'HttpSoft\Message\ResponseFactory';
-    protected static string $streamFactoryClass = 'HttpSoft\Message\StreamFactory';
-    protected static string $serverRequestCreatorClass = 'HttpSoft\ServerRequest\ServerRequestCreator';
-    protected static string $serverRequestCreatorMethod = 'createFromGlobals';
+    protected static string $responseFactoryClass = 'HttpSoft\\Message\\ResponseFactory';
+
+protected static string $streamFactoryClass = 'HttpSoft\\Message\\StreamFactory';
+
+protected static string $serverRequestCreatorClass = 'HttpSoft\\ServerRequest\\ServerRequestCreator';
+
+protected static string $serverRequestCreatorMethod = 'createFromGlobals';
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
