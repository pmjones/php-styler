<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Rule\LineRule\MergeParenBrace;
use PhpStyler\Rule\LineRule\NormalizeTrailingCommas;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PhpStyler\TestFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SplitterTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide44() : array
    {
        /** @php-styler-expansive */
        return [
            'short-line-unchanged' => [
                <<<'CODE'
                <?php
                echo foo($a, $b);
                CODE,
                <<<'EXPECT'
                <?php
                echo foo($a, $b);

                EXPECT,
            ],

            'comma-function-args' => [
                <<<'CODE'
                <?php
                someFunction($veryLongArgumentOne, $veryLongArgumentTwo, $veryLongArgumentThree);
                CODE,
                <<<'EXPECT'
                <?php

                someFunction(
                    $veryLongArgumentOne,
                    $veryLongArgumentTwo,
                    $veryLongArgumentThree,
                );

                EXPECT,
            ],

            'comma-array-literal' => [
                <<<'CODE'
                <?php
                $arr = [$veryLongValueOne, $veryLongValueTwo, $veryLongValueThree];
                CODE,
                <<<'EXPECT'
                <?php

                $arr = [
                    $veryLongValueOne,
                    $veryLongValueTwo,
                    $veryLongValueThree,
                ];

                EXPECT,
            ],

            'fluent-method-chain' => [
                <<<'CODE'
                <?php
                $result = $object->methodOne()->methodTwo()->methodThree();
                CODE,
                <<<'EXPECT'
                <?php

                $result = $object->methodOne()
                    ->methodTwo()
                    ->methodThree();

                EXPECT,
            ],

            'operator-boolean-operators' => [
                <<<'CODE'
                <?php
                $result = $conditionOne && $conditionTwo && $conditionThree;
                CODE,
                <<<'EXPECT'
                <?php

                $result = $conditionOne
                    && $conditionTwo
                    && $conditionThree;

                EXPECT,
            ],

            'operator-dot-concatenation' => [
                <<<'CODE'
                <?php
                $result = $longStringOne . $longStringTwo . $longStringThree;
                CODE,
                <<<'EXPECT'
                <?php

                $result = $longStringOne
                    . $longStringTwo
                    . $longStringThree;

                EXPECT,
            ],

            'comma-function-params' => [
                <<<'CODE'
                <?php
                function foo($longParamAlpha, $longParamBravo, $longParamCharlie) {}
                CODE,
                <<<'EXPECT'
                <?php

                function foo(
                    $longParamAlpha,
                    $longParamBravo,
                    $longParamCharlie,
                ) {
                }

                EXPECT,
            ],

            'comma-array-bracket' => [
                <<<'CODE'
                <?php
                $x = [$longValueAlpha, $longValueBravo, $longValueCharlie];
                CODE,
                <<<'EXPECT'
                <?php

                $x = [
                    $longValueAlpha,
                    $longValueBravo,
                    $longValueCharlie,
                ];

                EXPECT,
            ],

            'comma-trailing-comma' => [
                <<<'CODE'
                <?php
                someFunc($longArgAlpha, $longArgBravo, $longArgCharlie,);
                CODE,
                <<<'EXPECT'
                <?php

                someFunc(
                    $longArgAlpha,
                    $longArgBravo,
                    $longArgCharlie,
                );

                EXPECT,
            ],

            'comma-two-elements' => [
                <<<'CODE'
                <?php
                someFunc($veryLongArgumentAlpha, $veryLongArgumentBravo);
                CODE,
                <<<'EXPECT'
                <?php

                someFunc(
                    $veryLongArgumentAlpha,
                    $veryLongArgumentBravo,
                );

                EXPECT,
            ],

            'comma-use-variables' => [
                <<<'CODE'
                <?php
                $fn = function () use ($longVarAlpha, $longVarBravo, $longVarCharlie) {};
                CODE,
                <<<'EXPECT'
                <?php

                $fn = function () use (
                    $longVarAlpha,
                    $longVarBravo,
                    $longVarCharlie,
                ) {
                };

                EXPECT,
            ],

            'comma-attribute-args' => [
                <<<'CODE'
                <?php
                #[SomeAttribute($longArgAlpha, $longArgBravo, $longArgCharlie)]
                class Foo {}
                CODE,
                <<<'EXPECT'
                <?php

                #[SomeAttribute(
                    $longArgAlpha,
                    $longArgBravo,
                    $longArgCharlie,
                )]
                class Foo
                {
                }

                EXPECT,
            ],

            'comma-nested-array' => [
                <<<'CODE'
                <?php
                $x = [$a, [$longInnerAlpha, $longInnerBravo, $longInnerCharlie], $d];
                CODE,
                <<<'EXPECT'
                <?php

                $x = [
                    $a,
                    [
                        $longInnerAlpha,
                        $longInnerBravo,
                        $longInnerCharlie,
                    ],
                    $d,
                ];

                EXPECT,
            ],

            'fluent-nullsafe-chain' => [
                <<<'CODE'
                <?php
                $x = $obj?->alphaMethod()?->bravoMethod()?->charlieMethod();
                CODE,
                <<<'EXPECT'
                <?php

                $x = $obj?->alphaMethod()
                    ?->bravoMethod()
                    ?->charlieMethod();

                EXPECT,
            ],

            'fluent-mixed-chain' => [
                <<<'CODE'
                <?php
                $x = $object->alphaMethod()?->bravoMethod()->charlieMethod();
                CODE,
                <<<'EXPECT'
                <?php

                $x = $object->alphaMethod()
                    ?->bravoMethod()
                    ->charlieMethod();

                EXPECT,
            ],

            'operator-boolean-or' => [
                <<<'CODE'
                <?php
                $x = $longCondAlpha || $longCondBravo || $longCondCharlie;
                CODE,
                <<<'EXPECT'
                <?php

                $x = $longCondAlpha
                    || $longCondBravo
                    || $longCondCharlie;

                EXPECT,
            ],

            'operator-ternary' => [
                <<<'CODE'
                <?php
                $x = $someCondition ? $longValueAlpha : $longValueBravo;
                CODE,
                <<<'EXPECT'
                <?php

                $x = $someCondition
                    ? $longValueAlpha
                    : $longValueBravo;

                EXPECT,
            ],

            'operator-coalesce' => [
                <<<'CODE'
                <?php
                $x = $longValueAlpha ?? $longValueBravo ?? $longValueCharlie;
                CODE,
                <<<'EXPECT'
                <?php

                $x = $longValueAlpha
                    ?? $longValueBravo
                    ?? $longValueCharlie;

                EXPECT,
            ],

            'comma-new-args' => [
                <<<'CODE'
                <?php
                $x = new SomeClassName($longArgAlpha, $longArgBravo, $longArgCharlie);
                CODE,
                <<<'EXPECT'
                <?php

                $x = new SomeClassName(
                    $longArgAlpha,
                    $longArgBravo,
                    $longArgCharlie,
                );

                EXPECT,
            ],

            'comma-static-call' => [
                <<<'CODE'
                <?php
                SomeClass::staticMethod($longArgAlpha, $longArgBravo, $longArgCharlie);
                CODE,
                <<<'EXPECT'
                <?php

                SomeClass::staticMethod(
                    $longArgAlpha,
                    $longArgBravo,
                    $longArgCharlie,
                );

                EXPECT,
            ],

            'comma-nested-calls' => [
                <<<'CODE'
                <?php
                foo(bar($longArgAlpha), baz($longArgBravo), qux($longArgCharlie));
                CODE,
                <<<'EXPECT'
                <?php

                foo(
                    bar($longArgAlpha),
                    baz($longArgBravo),
                    qux($longArgCharlie),
                );

                EXPECT,
            ],

            'comma-array-arrows' => [
                <<<'CODE'
                <?php
                $x = ["alpha" => $longValAlpha, "bravo" => $longValBravo, "charlie" => $longValCharlie];
                CODE,
                <<<'EXPECT'
                <?php

                $x = [
                    "alpha" => $longValAlpha,
                    "bravo" => $longValBravo,
                    "charlie" => $longValCharlie,
                ];

                EXPECT,
            ],

            'paren-single-element' => [
                <<<'CODE'
                <?php
                foo($veryLongVariableNameThatIsTheSoleArgument);
                CODE,
                <<<'EXPECT'
                <?php

                foo(
                    $veryLongVariableNameThatIsTheSoleArgument,
                );

                EXPECT,
            ],

            'comma-method-call-args' => [
                <<<'CODE'
                <?php
                $obj->someMethod($longArgAlpha, $longArgBravo, $longArgCharlie);
                CODE,
                <<<'EXPECT'
                <?php

                $obj->someMethod(
                    $longArgAlpha,
                    $longArgBravo,
                    $longArgCharlie,
                );

                EXPECT,
            ],

            'comma-function-params-comment' => [
                <<<'CODE'
                <?php
                function foo($longParamAlpha, $longParamBravo, $longParamCharlie) // comment
                {
                }
                CODE,
                <<<'EXPECT'
                <?php

                function foo(
                    $longParamAlpha,
                    $longParamBravo,
                    $longParamCharlie,
                ) // comment
                {
                }

                EXPECT,
            ],

            'comma-typed-params' => [
                <<<'CODE'
                <?php
                function foo(string $longAlpha, int $longBravo, bool $longCharlie) : void {}
                CODE,
                <<<'EXPECT'
                <?php

                function foo(
                    string $longAlpha,
                    int $longBravo,
                    bool $longCharlie,
                ) : void
                {
                }

                EXPECT,
            ],

            'fluent-property-chain' => [
                <<<'CODE'
                <?php
                $x = $object->longPropertyAlpha->longPropertyBravo->longPropertyCharlie;
                CODE,
                <<<'EXPECT'
                <?php

                $x = $object->longPropertyAlpha
                    ->longPropertyBravo
                    ->longPropertyCharlie;

                EXPECT,
            ],

            'fluent-chain-with-args' => [
                <<<'CODE'
                <?php
                $x = $obj->alpha($a, $b)->bravo($c, $d)->charlie($e, $f);
                CODE,
                <<<'EXPECT'
                <?php

                $x = $obj->alpha($a, $b)
                    ->bravo($c, $d)
                    ->charlie($e, $f);

                EXPECT,
            ],

            'operator-mixed-boolean' => [
                <<<'CODE'
                <?php
                $x = $condAlpha && $condBravo || $condCharlie && $condDelta;
                CODE,
                <<<'EXPECT'
                <?php

                $x = $condAlpha && $condBravo
                    || $condCharlie && $condDelta;

                EXPECT,
            ],

            'operator-dot-strings' => [
                <<<'CODE'
                <?php
                $x = "prefix_" . $longMiddlePart . "_suffix_extra_long";
                CODE,
                <<<'EXPECT'
                <?php

                $x = "prefix_"
                    . $longMiddlePart
                    . "_suffix_extra_long";

                EXPECT,
            ],

            'paren-bracket-access' => [
                <<<'CODE'
                <?php
                $x = $veryLongArrayName[$veryLongIndexVariableName];
                CODE,
                <<<'EXPECT'
                <?php

                $x = $veryLongArrayName[
                    $veryLongIndexVariableName
                ];

                EXPECT,
            ],

            // Condition context: operators split before fluent inside if()
            'condition-operator-over-fluent' => [
                <<<'CODE'
                <?php
                if ($obj->checkAlpha() && $obj->checkBravo() && $obj->checkCharlie()) {}
                CODE,
                <<<'EXPECT'
                <?php

                if (
                    $obj->checkAlpha()
                    && $obj->checkBravo()
                    && $obj->checkCharlie()
                ) {
                }

                EXPECT,
            ],

            'unsplittable-line' => [
                <<<'CODE'
                <?php
                $veryLongVariableNameThatCannotBeSplitAtAllBecauseItIsOneToken = 1;
                CODE,
                <<<'EXPECT'
                <?php
                $veryLongVariableNameThatCannotBeSplitAtAllBecauseItIsOneToken = 1;

                EXPECT,
            ],

        ];
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide88() : array
    {
        /** @php-styler-expansive */
        return [
            'comma-over-fluent' => [
                <<<'CODE'
                <?php
                someLongFunctionName($firstObject->someLongMethodName(), $secondObject->anotherLongMethodName());
                CODE,
                <<<'EXPECT'
                <?php

                someLongFunctionName(
                    $firstObject->someLongMethodName(),
                    $secondObject->anotherLongMethodName(),
                );

                EXPECT,
            ],

            'reentrant-split' => [
                <<<'CODE'
                <?php
                foo($conditionAlpha && $conditionBravo && $conditionCharlie, $conditionDelta && $conditionEcho && $conditionFoxtrot);
                CODE,
                <<<'EXPECT'
                <?php

                foo(
                    $conditionAlpha && $conditionBravo && $conditionCharlie,
                    $conditionDelta && $conditionEcho && $conditionFoxtrot,
                );

                EXPECT,
            ],

            'comma-typed-params-defaults' => [
                <<<'CODE'
                <?php
                function processUserData(string $firstName, string $lastName, int $age = 0, bool $isActive = true) : void {}
                CODE,
                <<<'EXPECT'
                <?php

                function processUserData(
                    string $firstName,
                    string $lastName,
                    int $age = 0,
                    bool $isActive = true,
                ) : void
                {
                }

                EXPECT,
            ],

            'comma-constructor-promotion' => [
                <<<'CODE'
                <?php
                class Foo { public function __construct(private readonly string $firstName, private readonly string $lastName) {} }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function __construct(
                        private readonly string $firstName,
                        private readonly string $lastName,
                    ) {
                    }
                }

                EXPECT,
            ],

            'comma-nested-indent' => [
                <<<'CODE'
                <?php
                class UserService {
                    public function findActiveUsersByDepartment(string $departmentName, int $limit = 50, int $offset = 0) : array {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class UserService
                {
                    public function findActiveUsersByDepartment(
                        string $departmentName,
                        int $limit = 50,
                        int $offset = 0,
                    ) : array
                    {
                    }
                }

                EXPECT,
            ],

            'comma-array-arrows-assoc' => [
                <<<'CODE'
                <?php
                $config = ["database_host" => $hostAddress, "database_port" => $portNumber, "database_name" => $databaseName];
                CODE,
                <<<'EXPECT'
                <?php

                $config = [
                    "database_host" => $hostAddress,
                    "database_port" => $portNumber,
                    "database_name" => $databaseName,
                ];

                EXPECT,
            ],

            'comma-reentrant-operator' => [
                <<<'CODE'
                <?php
                processData($alphaCondition && $bravoCondition && $charlieCondition, $deltaCondition || $echoCondition || $foxtrotCondition, $golfValue ?? $hotelValue);
                CODE,
                <<<'EXPECT'
                <?php

                processData(
                    $alphaCondition && $bravoCondition && $charlieCondition,
                    $deltaCondition || $echoCondition || $foxtrotCondition,
                    $golfValue ?? $hotelValue,
                );

                EXPECT,
            ],

            'fluent-chain' => [
                <<<'CODE'
                <?php
                $response = $httpClient->withHeaders($headers)->withTimeout(30)->sendRequest($request)->getBody();
                CODE,
                <<<'EXPECT'
                <?php

                $response = $httpClient->withHeaders($headers)
                    ->withTimeout(30)
                    ->sendRequest($request)
                    ->getBody();

                EXPECT,
            ],

            'fluent-nullsafe' => [
                <<<'CODE'
                <?php
                $result = $container->getService("auth")?->getUser()?->getProfile()->getDisplayName()->toString();
                CODE,
                <<<'EXPECT'
                <?php

                $result = $container->getService("auth")
                    ?->getUser()
                    ?->getProfile()
                    ->getDisplayName()
                    ->toString();

                EXPECT,
            ],

            'operator-boolean-chain' => [
                <<<'CODE'
                <?php
                $isValid = $hasPermission && $isAuthenticated && $isNotExpired && $hasValidToken && $isNotBlocked;
                CODE,
                <<<'EXPECT'
                <?php

                $isValid = $hasPermission
                    && $isAuthenticated
                    && $isNotExpired
                    && $hasValidToken
                    && $isNotBlocked;

                EXPECT,
            ],

            'operator-concat-chain' => [
                <<<'CODE'
                <?php
                $message = "Hello " . $firstName . " " . $lastName . ", welcome to " . $applicationName . "!";
                CODE,
                <<<'EXPECT'
                <?php

                $message = "Hello "
                    . $firstName
                    . " "
                    . $lastName
                    . ", welcome to "
                    . $applicationName
                    . "!";

                EXPECT,
            ],

            'operator-coalesce-chain' => [
                <<<'CODE'
                <?php
                $result = $valueAlpha ?? $valueBravo ?? $valueCharlie ?? $valueDelta ?? $valueEcho ?? $valueFoxtrot;
                CODE,
                <<<'EXPECT'
                <?php

                $result = $valueAlpha
                    ?? $valueBravo
                    ?? $valueCharlie
                    ?? $valueDelta
                    ?? $valueEcho
                    ?? $valueFoxtrot;

                EXPECT,
            ],

            // Fluent chain where a middle call has enough args to trigger comma split
            'fluent-then-comma-args' => [
                <<<'CODE'
                <?php
                $result = $repository->findByStatus("active")->filterBy($conditionAlpha, $conditionBravo, $conditionCharlie)->sortBy("name")->paginate(25);
                CODE,
                <<<'EXPECT'
                <?php

                $result = $repository->findByStatus("active")
                    ->filterBy($conditionAlpha, $conditionBravo, $conditionCharlie)
                    ->sortBy("name")
                    ->paginate(25);

                EXPECT,
            ],

            // Fluent chain then comma split on the last call
            'fluent-trailing-comma' => [
                <<<'CODE'
                <?php
                $result = $service->authenticate($credentials)->authorize($resource)->execute($paramAlpha, $paramBravo, $paramCharlie, $paramDelta);
                CODE,
                <<<'EXPECT'
                <?php

                $result = $service->authenticate($credentials)
                    ->authorize($resource)
                    ->execute($paramAlpha, $paramBravo, $paramCharlie, $paramDelta);

                EXPECT,
            ],

            // Fluent first, then one call's args trigger comma split
            'fluent-first-then-comma' => [
                <<<'CODE'
                <?php
                $query = $database->table("users")->select($columnAlpha, $columnBravo, $columnCharlie, $columnDelta)->where("active", true)->orderBy("name")->get();
                CODE,
                <<<'EXPECT'
                <?php

                $query = $database->table("users")
                    ->select($columnAlpha, $columnBravo, $columnCharlie, $columnDelta)
                    ->where("active", true)
                    ->orderBy("name")
                    ->get();

                EXPECT,
            ],

            // P1 outer with nested calls as elements
            'comma-nested-calls' => [
                <<<'CODE'
                <?php
                $result = outerFunction(innerFunctionAlpha($alphaArgOne, $alphaArgTwo, $alphaArgThree), innerFunctionBravo($bravoArgOne, $bravoArgTwo, $bravoArgThree));
                CODE,
                <<<'EXPECT'
                <?php

                $result = outerFunction(
                    innerFunctionAlpha($alphaArgOne, $alphaArgTwo, $alphaArgThree),
                    innerFunctionBravo($bravoArgOne, $bravoArgTwo, $bravoArgThree),
                );

                EXPECT,
            ],

            // P1 array with P3 operator expressions as values
            'comma-array-operator-values' => [
                <<<'CODE'
                <?php
                $conditions = [$alphaValue && $bravoValue && $charlieValue, $deltaValue || $echoValue || $foxtrotValue, $golfValue ?? $hotelValue ?? $indiaValue];
                CODE,
                <<<'EXPECT'
                <?php

                $conditions = [
                    $alphaValue && $bravoValue && $charlieValue,
                    $deltaValue || $echoValue || $foxtrotValue,
                    $golfValue ?? $hotelValue ?? $indiaValue,
                ];

                EXPECT,
            ],

            // P2 static chain with P1 array arg
            'fluent-static-comma-array' => [
                <<<'CODE'
                <?php
                $records = UserModel::query()->where("department", $departmentName)->whereIn("status", [$statusAlpha, $statusBravo, $statusCharlie])->get();
                CODE,
                <<<'EXPECT'
                <?php

                $records = UserModel::query()
                    ->where("department", $departmentName)
                    ->whereIn("status", [$statusAlpha, $statusBravo, $statusCharlie])
                    ->get();

                EXPECT,
            ],

            // Return with P3 inside function body
            'return-operator' => [
                <<<'CODE'
                <?php
                function check() { return $hasAlphaPermission && $hasBravoPermission && $hasCharliePermission && $hasDeltaPermission; }
                CODE,
                <<<'EXPECT'
                <?php
                function check()
                {
                    return $hasAlphaPermission
                        && $hasBravoPermission
                        && $hasCharliePermission
                        && $hasDeltaPermission;
                }

                EXPECT,
            ],

            // Class method with P1 params, body has P2 return
            'method-comma-return-fluent' => [
                <<<'CODE'
                <?php
                class Processor { public function transform(string $inputData, callable $transformerCallback, array $transformerOptions) : TransformResult { return $this->pipeline->prepare($inputData)->apply($transformerCallback)->finalize(); } }
                CODE,
                <<<'EXPECT'
                <?php
                class Processor
                {
                    public function transform(
                        string $inputData,
                        callable $transformerCallback,
                        array $transformerOptions,
                    ) : TransformResult
                    {
                        return $this->pipeline
                            ->prepare($inputData)
                            ->apply($transformerCallback)
                            ->finalize();
                    }
                }

                EXPECT,
            ],

            // Constructor promotion with trailing comma and rejoinOrphanBefore
            'comma-promoted-trailing-comma' => [
                <<<'CODE'
                <?php
                class Config { public function __construct(private readonly string $databaseHost, private readonly int $databasePort, private readonly string $databaseName, private readonly string $databaseUser,) {} }
                CODE,
                <<<'EXPECT'
                <?php
                class Config
                {
                    public function __construct(
                        private readonly string $databaseHost,
                        private readonly int $databasePort,
                        private readonly string $databaseName,
                        private readonly string $databaseUser,
                    ) {
                    }
                }

                EXPECT,
            ],

        ];
    }

    private function assertSplit(string $code, string $expect, int $lineLen) : void
    {
        $styler = new Styler(
            new TestFormat(
                lineLen: $lineLen,
                rules: [
                    MergeParenBrace::class,
                    NormalizeTrailingCommas::class,
                    RemoveTrailingBlankLines::class,
                ],
            ),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    #[DataProvider('provide44')]
    public function test44(string $code, string $expect) : void
    {
        $this->assertSplit($code, $expect, 44);
    }

    #[DataProvider('provide88')]
    public function test88(string $code, string $expect) : void
    {
        $this->assertSplit($code, $expect, 88);
    }
}
