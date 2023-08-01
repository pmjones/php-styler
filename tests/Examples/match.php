<?php
$foo = match ($bar) {
    'baz', 'dib' => 'zim',
    'dir', 'irk' => 'doom',
    default => 'foo',
};
