<?php
function fluent_chain_examples()
{
    // two independent chains separated by assignment
    $this->longPropertyName = $this->veryLongMethodAlpha()
        ->veryLongMethodBravo()
        ->veryLongMethodCharlie();

    // static property chain: ::$prop is part of the chain
    $result = SomeClassName::$longStaticProperty
        ->veryLongMethodAlpha()
        ->veryLongMethodBravo();

    // static method chain
    $result = SomeClassName::veryLongStaticMethodName()
        ->veryLongMethodAlpha()
        ->veryLongMethodBravo();

    // nullsafe chain not orphaned
    $this->longPropertyName = $obj?->veryLongMethodAlpha()
        ?->veryLongMethodBravo()
        ?->veryLongMethodCharlie();

    // function call result chained
    $result = getProcessor()
        ->veryLongMethodAlpha()
        ->veryLongMethodBravo()
        ->veryLongMethodCharlie();

    // return with chain
    return $this->veryLongPropertyName
        ->veryLongMethodAlpha()
        ->veryLongMethodBravo()
        ->veryLongMethodCharlie();

    // chain in method argument
    $this->execute(
        $this->veryLongMethodAlpha()->veryLongMethodBravo()->veryLongMethodCharlie(),
    );
}
