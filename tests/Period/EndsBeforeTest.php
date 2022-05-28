<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\DateTime\DateTimeImmutable,
    Fyre\Period\Period;

trait EndsBeforeTest
{

    public function testEndsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBefore(new DateTimeImmutable('2022-01-15'))
        );
    }

    public function testEndsBeforeBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBefore(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsBeforeBeforeExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsBefore(new DateTimeImmutable('2022-01-14'))
        );
    }

    public function testEndsBeforeAfter(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBefore(new DateTimeImmutable('2022-01-16'))
        );
    }

    public function testEndsBeforeAfterExcludeEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsBefore(new DateTimeImmutable('2022-01-15'))
        );
    }

}
