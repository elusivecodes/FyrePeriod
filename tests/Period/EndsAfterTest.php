<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait EndsAfterTest
{

    public function testEndsAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsAfter(new DateTimeImmutable('2022-01-15'))
        );
    }

    public function testEndsAfterBefore(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsAfter(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsAfterBeforeExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsAfter(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsAfterAfter(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsAfter(new DateTimeImmutable('2022-01-16'))
        );
    }

    public function testEndsAfterAfterExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsAfter(new DateTimeImmutable('2022-01-15'))
        );
    }

}
