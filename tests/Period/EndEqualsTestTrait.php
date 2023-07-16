<?php
declare(strict_types=1);

namespace Tests\Period;

use Fyre\DateTime\DateTime;
use Fyre\Period\Period;

trait EndEqualsTestTrait
{

    public function testEndEquals(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endEquals(new DateTime('2022-01-15'))
        );
    }

    public function testEndEqualsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endEquals(new DateTime('2022-01-14'))
        );
    }

    public function testEndEqualsBeforeExcludeEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', excludeBoundaries: 'end'))
                ->endEquals(new DateTime('2022-01-14'))
        );
    }

    public function testEndEqualsAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endEquals(new DateTime('2022-01-14'))
        );
    }

    public function testEndEqualsAfterExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', excludeBoundaries: 'end'))
                ->endEquals(new DateTime('2022-01-15'))
        );
    }

}
