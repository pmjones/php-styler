<?php
// empty with long method chain that forces expansion
if (
    empty(
        $this->veryLongPropertyName
        ->getAnotherLongPropertyChain()
        ->retrieveYetAnotherVeryLongProperty()
    )
) {
    echo "empty";
}

// empty with nested call
$result = empty(
    $container->getServiceLocator()
    ->findRegisteredServiceByFullyQualifiedIdentifier($longIdentifierName)
);

// eval with long expression, no trailing comma
eval(
    $configLoader->loadFromFile($configDirectory . "/very-long-config-filename.php")
    ->toString()
);

// eval with long concatenation, no trailing comma
eval(
    "namespace "
    . $this->veryLongNamespaceName
    . ";"
    . $this->generateVeryLongClassDefinition()
);

// isset still gets trailing comma when expanded
isset(
    $this->veryLongAlphaPropertyName,
    $this->veryLongBravoPropertyName,
    $this->veryLongCharliePropertyName,
);

// unset still gets trailing comma when expanded
unset(
    $this->veryLongAlphaPropertyName,
    $this->veryLongBravoPropertyName,
    $this->veryLongCharliePropertyName,
);
