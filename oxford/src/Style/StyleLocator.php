<?php
declare(strict_types=1);

namespace Oxford\Style;

use Oxford\Token\T;

class StyleLocator
{
    /** @var array<class-string<T>, Style> */
    private array $instances = [];

    /**
     * @param class-string<T> $class
     */
    public function get(string $class) : Style
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        $shortName = substr($class, strrpos($class, '\\') + 1);
        $styleClass = "Oxford\\Style\\{$shortName}Style";

        /** @var Style $style */
        $style = class_exists($styleClass) ? new $styleClass() : new Style();
        $this->instances[$class] = $style;
        return $style;
    }
}
