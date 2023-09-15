<?php
switch ($foo) {
    case 'foo':
        // over-long comment 345678901234567890123456789012345678901234567890123456789012345678901234567890
        $lockId = substr($sessionId, 0, 64);
        break;

    case 'bar':
        // initial comment
        $i ++;
        break;

    case 'baz':
        $k ++;
        break;

    case 'dib':
    case 'zim':
    case 'gir':
        $j ++;
        break;

    default:
        $doom = 1;
}

// no default
switch ($foo) {
    case 'bar':
        $i ++;
        break;

    case 'baz':
        $k ++;
        break;

    case 'dib':
    case 'zim':
    case 'gir':
        $j ++;
        break;
}

// empty default
switch ($foo) {
    case 'dib':
    case 'zim':
    case 'gir':
        $j ++;
        break;

    default:
}
