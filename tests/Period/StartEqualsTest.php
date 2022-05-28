<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait StartEqualsTest
{

    public function testStartEquals(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startEquals(new DateTimeImmutable('2022-01-01'))
        );
    }

    public function testStartEqualsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startEquals(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartEqualsBeforeExcludeStart(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startEquals(new DateTimeImmutable('2022-01-02'))
        );
    }

    public function testStartEqualsAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startEquals(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartEqualsAfterExcludeStart(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startEquals(new DateTimeImmutable('2022-01-01'))
        );
    }

}
