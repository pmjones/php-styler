<?php
// use raw string value
$sql = "
    SELECT TABLE_NAME
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = ?
    AND (TABLE_TYPE = 'BASE TABLE' OR TABLE_TYPE = 'VIEW')
    ORDER BY TABLE_NAME;
";

// use raw string value
$sql = '
    SELECT TABLE_NAME
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = ?
    AND (TABLE_TYPE = \'BASE TABLE\' OR TABLE_TYPE = \'VIEW\')
    ORDER BY TABLE_NAME;
';

// use raw string value
$foo = "foo\nbar\nbaz";
$foo = 'foo\\nbar\\nbaz';

// interpolation causes escaped newlines with literal \n
$foo = "%-{$max}s %s\nzim";
