<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait EndsBeforeOrEqualsTest
{

    public function testEndsBeforeOrEquals(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBeforeOrEquals(new DateTimeImmutable('2022-01-15'))
        );
    }

    public function testEndsBeforeOrEqualsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBeforeOrEquals(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsBeforeOrEqualsBeforeExcludeEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsBeforeOrEquals(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsBeforeOrEqualsAfter(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBeforeOrEquals(new DateTimeImmutable('2022-01-16'))
        );
    }

    public function testEndsBeforeOrEqualsAfterExcludeEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsBeforeOrEquals(new DateTimeImmutable('2022-01-15'))
        );
    }

}
