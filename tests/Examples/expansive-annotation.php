<?php
class Foo
{
    protected $prop1 = [Cast\Int_::class => [10, 1]];

    /** @php-styler-expansive */
    protected $prop2 = [
        Cast\Int_::class => [
            10,
            1,
        ],
        [
        ],
    ];

    protected $prop3 = [Cast\Int_::class => [10, 1]];

    /* @php-styler-expansive */
    protected $prop4 = [
        Cast\Int_::class => [
            10,
            1,
        ],
        [
        ],
    ];

    protected $prop5 = [Cast\Int_::class => [10, 1]];

    // @php-styler-expansive
    protected $prop6 = [
        Cast\Int_::class => [
            10,
            1,
        ],
        [
        ],
    ];

    protected $prop7 = [Cast\Int_::class => [10, 1]];

    // @php-styler-expansive
    protected $prop8 = [
        Cast\Int_::class => [
            10,
            1,
        ],
        [
        ],
    ];
}
