<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait EndsAfterOrEqualsTest
{

    public function testEndsAfterOrEquals(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsAfterOrEquals(new DateTimeImmutable('2022-01-15'))
        );
    }

    public function testEndsAfterOrEqualsBefore(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsAfterOrEquals(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsAfterOrEqualsBeforeExcludeEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsAfterOrEquals(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsAfterOrEqualsAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsAfterOrEquals(new DateTimeImmutable('2022-01-16'))
        );
    }

    public function testEndsAfterOrEqualsAfterExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsAfterOrEquals(new DateTimeImmutable('2022-01-15'))
        );
    }

}
