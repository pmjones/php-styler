<?php
// do not escape newlines without literal \n
$sql = "
    SELECT TABLE_NAME
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = ?
    AND (TABLE_TYPE = 'BASE TABLE' OR TABLE_TYPE = 'VIEW')
    ORDER BY TABLE_NAME;
";

// do not escape newlines without literal \n
$sql = "
    SELECT TABLE_NAME
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = '{$table_schema}'
    AND (TABLE_TYPE = 'BASE TABLE' OR TABLE_TYPE = 'VIEW')
    ORDER BY TABLE_NAME;
";

// do not escape newlines without literal \n
$sql = '
    SELECT TABLE_NAME
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = ?
    AND (TABLE_TYPE = \'BASE TABLE\' OR TABLE_TYPE = \'VIEW\')
    ORDER BY TABLE_NAME;
';

// do escape newlines with literal \n
$foo = "foo\nbar\nbaz";
$foo = 'foo\\nbar\\nbaz';
