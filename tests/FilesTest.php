<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;

class FilesTest extends TestCase
{
    /**
     * @var string[]
     */
    protected array $expect = [
        'Clip.php',
        'Command/Apply.php',
        'Command/ApplyOptions.php',
        'Command/Command.php',
        'Command/Preview.php',
        'Command/PreviewOptions.php',
        'Config.php',
        'Files.php',
        'Line.php',
        'Nesting.php',
        'Printable/Args.php',
        'Printable/ArrayDim.php',
        'Printable/Array_.php',
        'Printable/ArrowFunction.php',
        'Printable/As_.php',
        'Printable/AttributeGroup.php',
        'Printable/AttributeGroups.php',
        'Printable/Body.php',
        'Printable/BodyEmpty.php',
        'Printable/Break_.php',
        'Printable/Cast.php',
        'Printable/ClassConst.php',
        'Printable/Class_.php',
        'Printable/Closure.php',
        'Printable/ClosureUse.php',
        'Printable/Comment.php',
        'Printable/Comments.php',
        'Printable/Cond.php',
        'Printable/Const_.php',
        'Printable/Continue_.php',
        'Printable/DeclareDirective.php',
        'Printable/Declare_.php',
        'Printable/Do_.php',
        'Printable/DoubleArrow.php',
        'Printable/ElseIf_.php',
        'Printable/Else_.php',
        'Printable/Encapsed.php',
        'Printable/End.php',
        'Printable/EnumCase.php',
        'Printable/Enum_.php',
        'Printable/Expr.php',
        'Printable/Expression.php',
        'Printable/Extends_.php',
        'Printable/False_.php',
        'Printable/For_.php',
        'Printable/Foreach_.php',
        'Printable/Function_.php',
        'Printable/Goto_.php',
        'Printable/HaltCompiler.php',
        'Printable/Heredoc.php',
        'Printable/If_.php',
        'Printable/Implements_.php',
        'Printable/Infix.php',
        'Printable/InfixOp.php',
        'Printable/InlineHtml.php',
        'Printable/InstanceOp.php',
        'Printable/Interface_.php',
        'Printable/Label.php',
        'Printable/MatchArm.php',
        'Printable/Match_.php',
        'Printable/MemberOp.php',
        'Printable/Modifiers.php',
        'Printable/Namespace_.php',
        'Printable/New_.php',
        'Printable/Nowdoc.php',
        'Printable/Null_.php',
        'Printable/ParamName.php',
        'Printable/Params.php',
        'Printable/PostfixOp.php',
        'Printable/Precedence.php',
        'Printable/PrefixOp.php',
        'Printable/Printable.php',
        'Printable/Property.php',
        'Printable/ReservedArg.php',
        'Printable/ReservedStmt.php',
        'Printable/ReservedWord.php',
        'Printable/ReturnType.php',
        'Printable/Return_.php',
        'Printable/Separator.php',
        'Printable/StaticOp.php',
        'Printable/SwitchCase.php',
        'Printable/SwitchCaseDefault.php',
        'Printable/Switch_.php',
        'Printable/Ternary.php',
        'Printable/Throw_.php',
        'Printable/Trait_.php',
        'Printable/True_.php',
        'Printable/TryCatch.php',
        'Printable/TryFinally.php',
        'Printable/Try_.php',
        'Printable/Unset_.php',
        'Printable/UseImport.php',
        'Printable/UseTrait.php',
        'Printable/UseTraitAs.php',
        'Printable/UseTraitInsteadof.php',
        'Printable/While_.php',
        'Printable/Yield_.php',
        'Printer.php',
        'Service.php',
        'Split.php',
        'Styler.php',
        'Visitor.php',
    ];

    public function testDirs() : void
    {
        $dir = dirname(__DIR__) . '/src/';
        $len = strlen($dir);
        $files = new Files($dir);
        $actual = [];

        /** @var string $file */
        foreach ($files as $file) {
            $actual[] = substr($file, $len);
        }

        sort($actual);
        $this->assertSame($this->expect, $actual);
    }

    public function testFile() : void
    {
        $dir = dirname(__DIR__) . '/';
        $len = strlen($dir);
        $files = new Files($dir . 'php-styler.php');
        $actual = [];

        /** @var string $file */
        foreach ($files as $file) {
            $actual[] = substr($file, $len);
        }

        sort($actual);
        $this->assertSame(['php-styler.php'], $actual);
    }
}
