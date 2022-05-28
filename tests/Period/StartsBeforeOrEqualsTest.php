<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait StartsBeforeOrEqualsTest
{

    public function testStartsBeforeOrEquals(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsBeforeOrEquals(new DateTimeImmutable('2022-01-01'))
        );
    }

    public function testStartsBeforeOrEqualsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsBeforeOrEquals(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsBeforeOrEqualsBeforeExcludeStart(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsBeforeOrEquals(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsBeforeOrEqualsAfter(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsBeforeOrEquals(new DateTimeImmutable('2022-01-02'))
        );
    }

    public function testStartsBeforeOrEqualsAfterExcludeStart(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsBeforeOrEquals(new DateTimeImmutable('2022-01-01'))
        );
    }

}
