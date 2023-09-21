```php
// COMMENTING OUT THE SWITCH BODY DELETES THE BODY (IE ALL THE COMMENTS).
// PARSER DOES NOT EVEN SEE THOSE COMMENTS.
switch ($foo) {
    // this comment will be completely removed
}

// double-quotes increase backslashes; single-quotes do not
$foo = 'Name\Space\\';
$foo = "Name\\Space\\";
$foo = '/^foo\sbar$/';
$foo = "/^foo\sbar\$/";
