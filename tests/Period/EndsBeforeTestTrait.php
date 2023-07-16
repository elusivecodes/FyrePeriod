<?php
declare(strict_types=1);

namespace Tests\Period;

use Fyre\DateTime\DateTime;
use Fyre\Period\Period;

trait EndsBeforeTestTrait
{

    public function testEndsBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBefore(new DateTime('2022-01-15'))
        );
    }

    public function testEndsBeforeBefore(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBefore(new DateTime('2022-01-14'))
        );
    }

    public function testEndsBeforeBeforeExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsBefore(new DateTime('2022-01-14'))
        );
    }

    public function testEndsBeforeAfter(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15'))
                ->endsBefore(new DateTime('2022-01-16'))
        );
    }

    public function testEndsBeforeAfterExcludeEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-15', ['excludeBoundaries' => 'end']))
                ->endsBefore(new DateTime('2022-01-15'))
        );
    }

}
