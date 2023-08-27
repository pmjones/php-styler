<?php
function isFunctionCall(int $i) : bool
{
    return $this->phpTokens[$i]->is(T_STRING)
        && $this->nextSignificantToken($i)?->is('(')
        && ! $this->prevSignificantToken($i)?->is([
            T_OBJECT_OPERATOR,
            T_NULLSAFE_OBJECT_OPERATOR,
            T_DOUBLE_COLON,
            T_FUNCTION,
        ]);
}

if (true) {
    if (true) {
        if (true) {
            $value = $default ?? "";
            $placeholderAttr = [
                'value' => $value,
                'disabled' => true,
                'selected' => $selected == $default,
            ];

            throw new Exception\FileNotFound(''
                . PHP_EOL
                . "File: {$name}"
                . PHP_EOL
                . "Extension: {$this->extension}"
                . PHP_EOL
                . "Collection: "
                . ($collection === '' ? '(default)' : $collection)
                . PHP_EOL
                . "Paths: "
                . print_r($this->paths[$collection], true)
                . PHP_EOL
                . "Catalog class: "
                . print_r(get_class($this), true)
            );
        }
    }
}
