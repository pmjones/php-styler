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
