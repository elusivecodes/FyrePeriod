<?php
declare(strict_types=1);

namespace Tests\Period;

use
    Fyre\Period\Period,
    Fyre\Period\PeriodCollection,
    RuntimeException;

trait SubtractAllTest
{

    public function testSubtractAll(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-30');
        $period2 = new Period('2022-01-05', '2022-01-10');
        $period3 = new Period('2022-01-15', '2022-01-20');
        $collection = $period1->subtractAll($period2, $period3);

        $this->assertInstanceOf(
            PeriodCollection::class,
            $collection
        );

        $this->assertCount(
            3,
            $collection
        );

        $this->assertSame(
            '2022-01-01T00:00:00.000+00:00',
            $collection[0]->start()->toIsoString()
        );

        $this->assertSame(
            '2022-01-05T00:00:00.000+00:00',
            $collection[0]->end()->toIsoString()
        );

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $collection[1]->start()->toIsoString()
        );

        $this->assertSame(
            '2022-01-15T00:00:00.000+00:00',
            $collection[1]->end()->toIsoString()
        );

        $this->assertSame(
            '2022-01-20T00:00:00.000+00:00',
            $collection[2]->start()->toIsoString()
        );

        $this->assertSame(
            '2022-01-30T00:00:00.000+00:00',
            $collection[2]->end()->toIsoString()
        );
    }

    public function testSubtractAllNoOverlaps(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-05');
        $period2 = new Period('2022-01-10', '2022-01-15');
        $period3 = new Period('2022-01-15', '2022-01-20');
        $collection = $period1->subtractAll($period2, $period3);

        $this->assertCount(
            1,
            $collection
        );

        $this->assertSame(
            '2022-01-01T00:00:00.000+00:00',
            $collection[0]->start()->toIsoString()
        );

        $this->assertSame(
            '2022-01-05T00:00:00.000+00:00',
            $collection[0]->end()->toIsoString()
        );
    }

    public function testSubtractAllInvalidGranularity(): void
    {
        $this->expectException(RuntimeException::class);

        $period1 = new Period('2022-01-01', '2022-01-30');
        $period2 = new Period('2022-01-05', '2022-01-10');
        $period3 = new Period('2022-01-15', '2022-01-20', ['granularity' => 'hour']);

        $period1->subtractAll($period2, $period3);
    }

}
