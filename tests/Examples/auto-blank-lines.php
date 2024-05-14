<?php
if (($ifNoneMatchEtags = $request->getETags()) && null !== ($etag = $this->getEtag())) {
    if (0 == strncmp($etag, 'W/', 2)) {
        $etag = substr($etag, 2);
    }

    // Use weak comparison as per https://tools.ietf.org/html/rfc7232#section-3.2.
    foreach ($ifNoneMatchEtags as $ifNoneMatchEtag) {
        if (0 == strncmp($ifNoneMatchEtag, 'W/', 2)) {
            $ifNoneMatchEtag = substr($ifNoneMatchEtag, 2);
        }

        if ($ifNoneMatchEtag === $etag || '*' === $ifNoneMatchEtag) {
            $notModified = true;
            break;
        }
    }

    // Only do If-Modified-Since date comparison when If-None-Match is not present as per https://tools.ietf.org/html/rfc7232#section-3.3.
} elseif ($modifiedSince && $lastModified) {
    $notModified = strtotime($modifiedSince) >= strtotime($lastModified);
    // make sure comment stays
} else {
    $fooBarBase = 'do something else';
}

/**
 * Short line
 * Also a short line
 * A very very very very very very very very very very very very very very very very very very long line
 */
if (true) {
    $foo1->language
        ->evaluate(
            $this->expression,
            [
                'request' => $request,
                'method' => $request->getMethod(),
                'path' => rawurldecode($request->getPathInfo()),
            ],
        );

    $foo2->language;

    $foo3->language
        ->evaluate(
            $this->expression,
            [
                'request' => $request,
                'method' => $request->getMethod(),
                'path' => rawurldecode($request->getPathInfo()),
            ],
        );

    $foo4->language;

    $foo5->language
        ->evaluate(
            $this->expression,
            [
                'request' => $request,
                'method' => $request->getMethod(),
                'path' => rawurldecode($request->getPathInfo()),
            ],
        );
}

class Foo
{
    /**
     * @var array<class-string, array{string, string, string}>
     */
    protected array $operators = [
        Expr\Assign::class => [' ', '=', ' '],
        Expr\AssignOp\BitwiseAnd::class => [' ', '&=', ' '],
        Expr\AssignOp\BitwiseOr::class => [' ', '|=', ' '],
        Expr\AssignOp\BitwiseXor::class => [' ', '^=', ' '],
        Expr\AssignOp\Coalesce::class => [' ', '??=', ' '],
        Expr\AssignOp\Concat::class => [' ', '.=', ' '],
        Expr\AssignOp\Div::class => [' ', '/=', ' '],
        Expr\AssignOp\Minus::class => [' ', '-=', ' '],
    ];
}
