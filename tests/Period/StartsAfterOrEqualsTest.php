<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait StartsAfterOrEqualsTest
{

    public function testStartsAfterOrEquals(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsAfterOrEquals(new DateTimeImmutable('2022-01-01'))
        );
    }

    public function testStartsAfterOrEqualsBefore(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsAfterOrEquals(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsAfterOrEqualsBeforeExcludeStart(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsAfterOrEquals(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsAfterOrEqualsAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsAfterOrEquals(new DateTimeImmutable('2022-01-02'))
        );
    }

    public function testStartsAfterOrEqualsAfterExcludeStart(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsAfterOrEquals(new DateTimeImmutable('2022-01-01'))
        );
    }

}
