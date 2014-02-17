<?php

require __DIR__ . '/../vendor/autoload.php';

use SliceableStringy\SliceableStringy as S;

describe('SliceableStringy', function() {
    $this->string = S::create('Fòô Bàř', 'UTF-8');

    it('extends Stringy', function() {
        expect(new S('test'))->toBeAnInstanceOf('Stringy\Stringy');
    });

    it('implements the ArrayAccess interface', function() {
        expect(new S('test'))->toBeAnInstanceOf('\ArrayAccess');
    });

    context('given a positive string index,', function() {
        it('returns the character if it exists', function() {
            expect($this->string['2'])->toBe('ô');
        });

        it('throws an exception if out of range', function() {
            $outOfBounds = function() {
                $this->string['10'];
            };

            expect($outOfBounds)->toThrow('\OutOfBoundsException');
        });
    });

    context('given a negative string index,', function() {
        it('counts from the end of the string', function() {
            expect($this->string['-2'])->toBe('à');
        });

        it('throws an exception if out of range', function() {
            $outOfBounds = function() {
                $this->string['-10'];
            };

            expect($outOfBounds)->toThrow('\OutOfBoundsException');
        });
    });

    context('given a string index with too many colons', function() {
        it('throws an InvalidArgumentException', function() {
            $tooManyColons = function() {
                $this->string['1:2:3:4'];
            };

            expect($tooManyColons)->toThrow('\InvalidArgumentException');
        });
    });

    context('given valid slice notation,', function() {
        it('throws an exception if step is 0', function() {
            $invalidStep = function() {
                $this->string['1:2:0'];
            };

            expect($invalidStep)->toThrow('\InvalidArgumentException');
        });

        it('defaults to a step of 1 if not set', function() {
            expect((string) $this->string['::'])->toBe('Fòô Bàř');
        });

        it('returns a copy if start, stop and step are empty', function() {
            $copy = $this->string[':'];

            expect((string) $copy)->toBe('Fòô Bàř');
            expect($copy)->notToEqual($this->string);
        });

        it('returns the remaining string if stop is not given', function() {
            expect((string) $this->string['4:'])->toBe('Bàř');
        });

        it('returns characters up to, but not including the end index', function() {
            expect((string) $this->string['4:6'])->toBe('Bà');
        });

        it('accepts a negative start index', function() {
            expect((string) $this->string['-1:'])->toBe('ř');
            expect((string) $this->string['-3:6'])->toBe('Bà');
        });

        it('accepts a negative end index', function() {
            expect((string) $this->string[':-1'])->toBe('Fòô Bà');
            expect((string) $this->string['4:-2'])->toBe('B');
        });

        it('returns an empty string if no indices are in the set', function() {
            expect((string) $this->string['2:-6'])->toBe('');
        });

        it('returns every nth char if step is set', function() {
            expect((string) $this->string['::2'])->toBe('FôBř');
            expect((string) $this->string['1:5:3'])->toBe('òB');
        });

        it('accepts a negative step to iterate in reverse', function() {
            expect((string) $this->string['::-1'])->toBe('řàB ôòF');
            expect((string) $this->string['-1::-2'])->toBe('řBôF');
            expect((string) $this->string['-1:-6:-1'])->toBe('řàB ôò');
        });
    });
});
