<?php
// braceless if with switch body
if (! $continue) {
    switch ($errorReason) {
        case "dupClockIn":
            $state = "alreadyClockedIn";
            break;
        case "badClockOut":
            $state = $errorReason;
            break;
        default:
            $state = "default";
            break;
    }
}

echo "after braceless if-switch";

// braceless if with for body
if ($condition) {
    for ($i = 0; $i < 10; $i ++) {
        echo $i;
    }
}

echo "after braceless if-for";

// braceless if with foreach body
if ($condition) {
    foreach ($items as $item) {
        echo $item;
    }
}

echo "after braceless if-foreach";

// braceless if with while body
if ($condition) {
    while ($running) {
        $running = check();
    }
}

echo "after braceless if-while";

// braceless if with if body
if ($a) {
    if ($b) {
        echo "nested";
    }
}

echo "after braceless if-if";

// braceless if with if-else body
if ($a) {
    if ($b) {
        echo "yes";
    } else {
        echo "no";
    }
}

echo "after braceless if-if-else";

// braceless if with try-catch body
if ($condition) {
    try {
        riskyOperation();
    } catch (Exception $e) {
        handleError($e);
    }
}

echo "after braceless if-try-catch";

// braceless if with try-catch-finally body
if ($condition) {
    try {
        riskyOperation();
    } catch (Exception $e) {
        handleError($e);
    } finally {
        cleanup();
    }
}

echo "after braceless if-try-catch-finally";

// switch inside try inside while (the original bug pattern)
while (true) {
    try {
        switch ($state) {
            case "a":
                if (! $x) {
                    switch ($y) {
                        default:
                            break;
                    }
                }

                break;
            default:
                break;
        }
    } catch (Exception $e) {
        echo $e;
    }
}
