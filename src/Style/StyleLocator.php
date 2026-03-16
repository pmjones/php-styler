<?php
declare(strict_types=1);

namespace PhpStyler\Style;

use PhpStyler\Token\T;

class StyleLocator
{
    /**
     * @var array<class-string<T>, Style>
     */
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
        $styleClass = "PhpStyler\\Style\\{$shortName}Style";

        /** @var Style $style */
        $style = class_exists($styleClass) ? new $styleClass() : new Style();
        $this->instances[$class] = $style;
        return $style;
    }
}
