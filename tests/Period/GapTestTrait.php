<?php
declare(strict_types=1);

namespace Tests\Period;

use Fyre\Period\Period;
use RuntimeException;

trait GapTestTrait
{

    public function testGap(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-15', '2022-01-20');
        $period3 = $period1->gap($period2);

        $this->assertInstanceOf(
            Period::class,
            $period3
        );
    }

    public function testGapStartAfter(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-15', '2022-01-20');
        $period3 = $period1->gap($period2);

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $period3->start()->toIsoString()
        );

        $this->assertFalse(
            $period3->includesStart()
        );
    }

    public function testGapStartAfterExcludeStart(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-15', '2022-01-20', ['excludeBoundaries' => 'start']);
        $period3 = $period1->gap($period2);

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $period3->start()->toIsoString()
        );

        $this->assertTrue(
            $period3->includesEnd()
        );
    }

    public function testGapEndAfter(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-15', '2022-01-20');
        $period3 = $period1->gap($period2);

        $this->assertSame(
            '2022-01-15T00:00:00.000+00:00',
            $period3->end()->toIsoString()
        );

        $this->assertFalse(
            $period3->includesEnd()
        );
    }

    public function testGapEndAfterExcludeEnd(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10', ['excludeBoundaries' => 'end']);
        $period2 = new Period('2022-01-15', '2022-01-20');
        $period3 = $period1->gap($period2);

        $this->assertSame(
            '2022-01-15T00:00:00.000+00:00',
            $period3->end()->toIsoString()
        );

        $this->assertTrue(
            $period3->includesStart()
        );
    }

    public function testGapOverlap(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-15');
        $period2 = new Period('2022-01-10', '2022-01-20');

        $this->assertNull(
            $period1->gap($period2)
        );
    }

    public function testGapTouches(): void
    {
        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-10', '2022-01-20');

        $this->assertNull(
            $period1->gap($period2)
        );
    }

    public function testGapInvalidGranularity(): void
    {
        $this->expectException(RuntimeException::class);

        $period1 = new Period('2022-01-01', '2022-01-10');
        $period2 = new Period('2022-01-15', '2022-01-20', ['granularity' => 'hour']);

        $period1->gap($period2);
    }

}
