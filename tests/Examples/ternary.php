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

// ternary in argument
$useTraitAs = new P\UseTraitAs(
    $node->trait ? $this->name($node->trait) : null,
    $this->name($node->method),
    $node->newModifier,
    $node->newName ? $this->name($node->newName) : null,
);
