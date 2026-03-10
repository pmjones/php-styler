<?php
// try catch
try {
    $i ++;
} catch (Exception $e) {
    $k ++;
}

// try finally
try {
    $i ++;
} finally {
    $j ++;
}

// try catch finally
try {
    $i ++;
} catch (Exception $e) {
    $k ++;
} finally {
    $j ++;
}

// catch without var
try {
    $i ++;
} catch (Exception) {
    $k ++;
}

// catch with fqcn
try {
    $i ++;
} catch (\Throwable) {
    $k ++;
}
