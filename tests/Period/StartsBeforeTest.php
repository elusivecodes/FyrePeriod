<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait StartsBeforeTest
{

    public function testStartsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsBefore(new DateTimeImmutable('2022-01-01'))
        );
    }

    public function testStartsBeforeBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsBefore(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsBeforeBeforeExcludeStart(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsBefore(new DateTimeImmutable('2021-12-31'))
        );
    }

    public function testStartsBeforeAfter(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->startsBefore(new DateTimeImmutable('2022-01-02'))
        );
    }

    public function testStartsBeforeAfterExcludeStart(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'start']))
                ->startsBefore(new DateTimeImmutable('2022-01-01'))
        );
    }

}
