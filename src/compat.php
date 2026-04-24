<?php

/**
 * Polyfill for token constants introduced in PHP 8.4.
 */
// @codeCoverageIgnoreStart
if (! defined('T_PUBLIC_SET')) {
    define('T_PUBLIC_SET', 5001);
    define('T_PROTECTED_SET', 5002);
    define('T_PRIVATE_SET', 5003);
}

if (! defined('T_PROPERTY_C')) {
    define('T_PROPERTY_C', 5004);
}

// @codeCoverageIgnoreEnd
