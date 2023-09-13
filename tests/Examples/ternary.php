<?php
$a ?: $b;
$a ? $b : $c;

// ternary vs member operator
$veryLongVariableName = $veryLongVariableName2 instanceof VeryLongClassName
    ? clone $veryLongVariableName2
    : $veryLongVariableName2->getResponse();

// ternary vs member operator
$foo = str_starts_with($veryLongVariableName, '.')
    ? $this->relative($name, $this->dirname(end($this->names)))
    : $veryLongVariableName;

// ternary vs arguments
$veryLongVariableName['selected'] = is_array($selected)
    ? in_array($veryLongVariableName['value'], $selected)
    : $veryLongVariableName['value'] == $selected;

// ternary vs arguments
$veryLongVariableName = is_array($veryLongVariableName)
    ? $veryLongVariableName[key($veryLongVariableName)]
    : $veryLongVariableName;

// ternary in new
$useTraitAs = new P\UseTraitAs(
    $node->trait ? $this->name($node->trait) : null,
    $this->name($node->method),
    $node->newModifier,
    $node->newName ? $this->name($node->newName) : null,
);

// short ternary in new
$useTraitAs = new P\UseTraitAs(
    $node->trait ?: $this->name($node->trait),
    $this->name($node->method),
    $node->newModifier,
    $node->newName ?: $this->name($node->newName),
);

// ternary in method
$useTraitAs = $this->veryLongFunctionName(
    $node->trait ? $this->name($node->trait) : null,
    $this->name($node->method),
    $node->newModifier,
    $node->newName ? $this->name($node->newName) : null,
);

// short ternary in func
$useTraitAs = $this->veryLongFunctionName(
    $node->trait ?: $this->name($node->trait),
    $this->name($node->method),
    $node->newModifier,
    $node->newName ?: $this->name($node->newName),
);

// ternary embedded in argument with boolean looks off
$sourceDirs = explode('/', isset($basePath[0])
    && '/' === $basePath[0] ? substr($basePath, 1) : $basePath);

// fix by extracting the condition
$condition = isset($basePath[0]) && '/' === $basePath[0];
$sourceDirs = explode('/', $condition ? substr($basePath, 1) : $basePath);

if (true) {
    if (true) {
        return implode(
            '',
            [
                $flags & Stmt\Class_::MODIFIER_FINAL ? 'final ' : '',
                $flags & Stmt\Class_::MODIFIER_ABSTRACT ? 'abstract ' : '',
                $flags & Stmt\Class_::MODIFIER_PUBLIC ? 'public ' : '',
                $flags & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '',
                $flags & Stmt\Class_::MODIFIER_PRIVATE ? 'private ' : '',
                $flags & Stmt\Class_::MODIFIER_STATIC ? 'static ' : '',
                $flags & Stmt\Class_::MODIFIER_READONLY ? 'readonly ' : '',
            ],
        );
    }
}

if (true) {
    if (true) {
        return implode(
            '',
            [
                $flags & Stmt\Class_::MODIFIER_FINAL ?: 'final ',
                $flags & Stmt\Class_::MODIFIER_ABSTRACT ?: 'abstract ',
                $flags & Stmt\Class_::MODIFIER_PUBLIC ?: 'public ',
                $flags & Stmt\Class_::MODIFIER_PROTECTED ?: 'protected ',
                $flags & Stmt\Class_::MODIFIER_PRIVATE ?: 'private ',
                $flags & Stmt\Class_::MODIFIER_STATIC ?: 'static ',
                $flags & Stmt\Class_::MODIFIER_READONLY ?: 'readonly ',
            ],
        );
    }
}

// embedded assignments look off
$newPath = ! isset($path[0])
    || '/' === $path[0]
    || false !== ($colonPos = strpos($path, ':'))
        && (
            $colonPos < ($slashPos = strpos($path, '/')) || false === $slashPos
        ) ? "./{$path}" : $path;

// fix by extracting the assignments
$colonPos = strpos($path, ':');
$slashPos = strpos($path, '/');
$cond = ! isset($path[0])
    || '/' === $path[0]
    || false !== $colonPos && $colonPos < $slashPos
    || false === $slashPos;
$path = $cond ? "./{$path}" : $path;

// split ternary before concat
if (true) {
    if (true) {
        $uri = $queryString !== ''
            ? $endpoint->getUri() . $uriGlue . $queryString
            : $endpoint->getUri();
    }
}
