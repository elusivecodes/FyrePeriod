<?php
declare(strict_types=1);

namespace Tests\Period;

use Fyre\Period\Period;
use RuntimeException;

trait OverlapAllTestTrait
{

    public function testOverlapAll(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-20');
        $period2 = new Period('2022-01-10', '2022-01-20');
        $period3 = new Period('2022-01-05', '2022-01-15');
        $period4 = $period1->overlapAll($period2, $period3);

        $this->assertInstanceOf(
            Period::class,
            $period4
        );

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $period4->start()->toIsoString()
        );

        $this->assertSame(
            '2022-01-15T00:00:00.000+00:00',
            $period4->end()->toIsoString()
        );
    }

    public function testOverlapAllNoOverlaps(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-05', '2022-01-20');
        $period3 = new Period('2022-01-25', '2022-01-30');

        $this->assertNull(
            $period1->overlapAll($period2, $period3)
        );
    }

    public function testOverlapAllInvalidGranularity(): void
    {
        $this->expectException(RuntimeException::class);

        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-05', '2022-01-20');
        $period3 = new Period('2022-01-25', '2022-01-30', ['granularity' => 'hour']);

        $period1->overlapAll($period2, $period3);
    }

}
