<?php
namespace MyNamespace;

use Something;

#[MyAttribute]
abstract class Bar extends Baz implements Dib, Zim, Gir
{
    public const FOOCON = 'FOOCON';

    private readonly ?string $bar;

    public static string $callablePattern = '!^([^\\:]+)\\:([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)$!';

    private ?ContainerInterface $container;

    protected int $count = 0;

    public function __construct(
        #[MyVeryVeryVeryVeryVeryVeryLongAttribute]
        public readonly ?string $foofoo,
        #[MyAttribute(
            foofofoo: 'barbarbar',
            bazbazbaz: 'dibdibdib',
            zimzimzim: 'girgirgir'
        )]
        public readonly ?string $barbar,
        #[MyVeryVeryVeryVeryVeryVeryLongAttribute]
        public readonly ?string $bazbaz,
    ) {
    }

    final private static function doom(bool $baz = false)
    {
    }

    abstract protected function irk();
}

abstract class VeryLongClassname extends VeryLongBaseClass implements VeryLongInterface
{
    public function __construct()
    {
    }
}

#[MyAttribute]
class WithAttributes
{
}

/**
 * Comment
 */
class WithComment
{
}

/**
 * Comment
 */
#[MyAttribute]
class WithAttributesAndComments
{
}

class Local extends \Global
{
}

abstract class ValueCollection extends ValueObject implements
    ArrayAccess,
    Countable,
    IteratorAggregate
{
    /**
     * @param mixed[] $items
     */
    public function __construct(protected readonly array $items = [])
    {
    }
}
