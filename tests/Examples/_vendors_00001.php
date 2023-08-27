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

// colaesce, ternary, and array
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

// expansive array
if (true) {
    if (true) {
        $this->options = array_merge(
            [
                'id_field' => '_id',
                'data_field' => 'data',
                'time_field' => 'time',
                'expiry_field' => 'expires_at',
            ],
            $options,
        );
        $this->getCollection()->updateOne(
            [$this->options['id_field'] => $sessionId],
            ['$set' => $fields],
            ['upsert' => true],
        );
    }
}
