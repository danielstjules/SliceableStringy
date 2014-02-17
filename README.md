SliceableStringy
================

Python-like string slicing in PHP. The class extends
[Stringy](https://github.com/danielstjules/Stringy), and implements the
`ArrayAccess` interface.

[![Build Status](https://travis-ci.org/danielstjules/SliceableStringy.png)](https://travis-ci.org/danielstjules/SliceableStringy)

## Installation

If you're using Composer to manage dependencies, you can include the following
in your composer.json file:

```
"require": {
    "danielstjules/sliceable-stringy": "dev-master"
}
```

Then, after running `composer update` or `php composer.phar update`, you can
load the class using Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## Overview

`SliceableStringy` returns a slice when passed a string offset containing
one or more colons. Up to 3 arguments may be passed: `'start:stop:step'`. Start,
which defaults to 0, indicates the starting index of the slice. Stop, which
defaults to the length of the string, indicates the exclusive boundary of the
range. And step allows the user to only include every nth character. All
args may be positive or negative, where a negative index counts back from the
end of the string.

Just like `Stringy`, `SliceableStringy` is immutable and returns a new
instance with each slice.

## Examples

```php
use SliceableStringy\SliceableStringy as S;

$sliceable = S::create('Fòô Bàř', 'UTF-8');
```

#### Returning the character at a given offset
```php
$sliceable[1];    // 'ò'
$sliceable['-2']; // 'à'
```

#### Slicing with start and end
```php
$sliceable[':'];   // 'Fòô Bàř'
$sliceable['4:'];  // 'Bàř'
$sliceable['4:6']; // 'Bà'
```

#### Negative indices
```php
$sliceable['-1:'];  // 'ř'
$sliceable[':-1'];  // 'Fòô Bà'
$sliceable['-3:6']; // 'Bà'
$sliceable['2:-6']; // ''
```

#### Passing a step
```php
$sliceable['::-1'];   // 'řàB ôòF'
$sliceable['::2'];    // ''FôBř''
$sliceable['-3::-2']; // 'BôF'
```

#### Possible exceptions
```php
$sliceable['1:2:3:4']; // InvalidArgumentException
$sliceable['::0'];     // InvalidArgumentException, step cannot equal 0
```

## TL;DR

Butchering two languages with a single library.
