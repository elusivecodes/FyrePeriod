<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait StartsAfterTest
{

    public function testStartsAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsAfter(new DateTimeImmutable('2022-01-01'))
        );
    }

    public function testStartsAfterBefore(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsAfter(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsAfterBeforeExcludeStart(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsAfter(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsAfterAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsAfter(new DateTimeImmutable('2022-01-02'))
        );
    }

    public function testStartsAfterAfterExcludeStart(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsAfter(new DateTimeImmutable('2022-01-01'))
        );
    }

}
