SliceableStringy
================

Python string slices in PHP. The class extends
[Stringy](https://github.com/danielstjules/Stringy), and implements the
`ArrayAccess` interface.

[![Build Status](https://travis-ci.org/danielstjules/SliceableStringy.png)](https://travis-ci.org/danielstjules/SliceableStringy)

* [Installation](#installation)
* [Overview](#overview)
* [Examples](#examples)
* [Implementation Fidelity](#implementation-fidelity)
* [TL;DR](#tldr)

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
one or more colons. Up to 3 arguments may be passed: `'start:stop:step'`.
Start, which indicates the starting index of the slice, defaults to the first
character in the string if step is positive, and the last character if negative.
Stop, which indicates the exclusive boundary of the range, defaults to the
length of the string if step is positive, and before the first character if
negative. And step allows the user to include only every nth character in the
result, with its sign determining the direction in which indices are sampled.

Just like `Stringy`, `SliceableStringy` is immutable and returns a new
instance with each slice.

## Examples

```php
use SliceableStringy\SliceableStringy as S;

$sliceable = S::create('Fòô Bàř', 'UTF-8');
```

#### Specific offset
```php
$sliceable[1];    // 'ò'
$sliceable['-2']; // 'à'
```

#### Using start and stop
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
$sliceable['::2'];    // 'FôBř'
$sliceable['-3::-2']; // 'BôF'
```

#### Possible exceptions
```php
$sliceable[20];        // OutOfBoundsException
$sliceable['1:2:3:4']; // InvalidArgumentException, too many slice args
$sliceable['::0'];     // InvalidArgumentException, step cannot equal 0
```

## Implementation Fidelity

A number of specs in `spec/SliceableStringySpec.php` assert that the library
mimics Python's native slice notation. On top of the handful of unit tests,
`spec/fixtures/resultGenerator.py` has been used to generate test fixtures.
Each of the slices in `expectedResults.csv` are checked against SliceableStringy
to ensure correct functionality.

## TL;DR

Butchering two languages with a single library.
