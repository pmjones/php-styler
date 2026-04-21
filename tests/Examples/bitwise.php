<?php
$a & $b;
$a | $b;
$a ^ $b;
$a &= $b;
$a |= $b;
$a ^= $b;
~$a;

const DEFAULT_FLAGS = JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
    | JSON_THROW_ON_ERROR;

const DEFAULT_MASK = JSON_HEX_TAG
    & JSON_HEX_APOS
    & JSON_HEX_AMP
    & JSON_HEX_QUOT
    & JSON_THROW_ON_ERROR;
