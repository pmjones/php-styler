<?php
class Example
{
    public function __construct(
        #[MyVeryVeryVeryVeryVeryVeryLongAttribute]
        public readonly ?string $configFile,
        #[MyAttribute(
            foofofoo: 'barbarbar',
            bazbazbaz: 'dibdibdib',
            zimzimzim: 'girgirgir'
        )]
        public readonly ?string $foo,
        #[MyVeryVeryVeryVeryVeryVeryLongAttribute]
        public readonly ?string $bar,
    ) {
    }
}
