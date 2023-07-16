<?php
declare(strict_types=1);

namespace Tests\Period;

use Fyre\DateTime\DateTime;
use Fyre\Period\Period;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PeriodTest extends TestCase
{

    use ContainsTestTrait;
    use DiffSymmetricTestTrait;
    use EndEqualsTestTrait;
    use EndsAfterTestTrait;
    use EndsAfterOrEqualsTestTrait;
    use EndsBeforeTestTrait;
    use EndsBeforeOrEqualsTestTrait;
    use EqualsTestTrait;
    use GapTestTrait;
    use IncludesTestTrait;
    use OverlapTestTrait;
    use OverlapAllTestTrait;
    use OverlapAnyTestTrait;
    use OverlapsWithTestTrait;
    use RenewTestTrait;
    use StartEqualsTestTrait;
    use StartsAfterTestTrait;
    use StartsAfterOrEqualsTestTrait;
    use StartsBeforeTestTrait;
    use StartsBeforeOrEqualsTestTrait;
    use SubtractTestTrait;
    use SubtractAllTestTrait;
    use TouchesTestTrait;

    public function testConstructor(): void
    {
        $period = new Period('2022-01-01', '2022-01-10');

        $start = $period->start();
        $end = $period->end();

        $this->assertInstanceOf(
            DateTime::class,
            $start
        );

        $this->assertInstanceOf(
            DateTime::class,
            $end
        );
    }

    public function testConstructorDateTime(): void
    {
        $start = new DateTime('2022-01-01');
        $end = new DateTime('2022-01-10');

        $period = new Period($start, $end);

        $periodStart = $period->start();
        $periodEnd = $period->end();

        $this->assertInstanceOf(
            DateTime::class,
            $periodStart
        );

        $this->assertInstanceOf(
            DateTime::class,
            $periodEnd
        );

        $this->assertTrue(
            $start->isSame($periodStart)
        );

        $this->assertTrue(
            $end->isSame($periodEnd)
        );
    }

    public function testConstructorEndBeforeStart(): void
    {
        $this->expectException(RuntimeException::class);

        new Period('2022-01-10', '2022-01-01');
    }

    public function testConstructorInvalidGranularity(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Period('2022-01-01', '2022-01-10', 'invalid');
    }

    public function testConstructorInvalidExcludeBoundaries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'invalid');
    }

    public function testLength(): void
    {
        $this->assertSame(
            9,
            (new Period('2022-01-01', '2022-01-10'))->length()
        );
    }

    public function testLengthExcludeStart(): void
    {
        $this->assertSame(
            8,
            (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'start'))->length()
        );
    }

    public function testLengthExcludeEnd(): void
    {
        $this->assertSame(
            8,
            (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'end'))->length()
        );
    }

    public function testLengthGranularity(): void
    {
        $this->assertSame(
            24,
            (new Period('2022-01-01', '2022-01-02', 'hour'))->length()
        );
    }

    public function testEnd(): void
    {
        $end = (new Period('2022-01-01', '2022-01-10'))->end();

        $this->assertInstanceOf(
            DateTime::class,
            $end
        );

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $end->toIsoString()
        );
    }

    public function testEndExcludeEnd(): void
    {
        $end = (new Period('2022-01-01', '2022-01-10'))->end();

        $this->assertInstanceOf(
            DateTime::class,
            $end
        );

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $end->toIsoString()
        );
    }

    public function testGranularity(): void
    {
        $this->assertSame(
            'hour',
            (new Period('2022-01-01', '2022-01-10', 'hour'))->granularity()
        );
    }

    public function testIncludedEnd(): void
    {
        $includedEnd = (new Period('2022-01-01', '2022-01-10'))->includedEnd();

        $this->assertInstanceOf(
            DateTime::class,
            $includedEnd
        );

        $this->assertSame(
            '2022-01-10T00:00:00.000+00:00',
            $includedEnd->toIsoString()
        );
    }

    public function testIncludedEndExcludeEnd(): void
    {
        $includedEnd = (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'end'))->includedEnd();

        $this->assertInstanceOf(
            DateTime::class,
            $includedEnd
        );

        $this->assertSame(
            '2022-01-09T00:00:00.000+00:00',
            $includedEnd->toIsoString()
        );
    }

    public function testIncludedStart(): void
    {
        $includedStart = (new Period('2022-01-01', '2022-01-10'))->includedStart();

        $this->assertInstanceOf(
            DateTime::class,
            $includedStart
        );

        $this->assertSame(
            '2022-01-01T00:00:00.000+00:00',
            $includedStart->toIsoString()
        );
    }

    public function testIncludedStartExcludeStart(): void
    {
        $includedStart = (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'start'))->includedStart();

        $this->assertInstanceOf(
            DateTime::class,
            $includedStart
        );

        $this->assertSame(
            '2022-01-02T00:00:00.000+00:00',
            $includedStart->toIsoString()
        );
    }

    public function testIncludesEnd(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-10'))->includesEnd()
        );
    }

    public function testIncludesEndExcludeEnd(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'end'))->includesEnd()
        );
    }

    public function testIncludesStart(): void
    {
        $this->assertTrue(
            (new Period('2022-01-01', '2022-01-10'))->includesStart()
        );
    }

    public function testIncludesStartExcludeStart(): void
    {
        $this->assertFalse(
            (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'start'))->includesStart()
        );
    }

    public function testStart(): void
    {
        $start = (new Period('2022-01-01', '2022-01-10'))->start();

        $this->assertInstanceOf(
            DateTime::class,
            $start
        );

        $this->assertSame(
            '2022-01-01T00:00:00.000+00:00',
            $start->toIsoString()
        );
    }

    public function testStartExcludeStart(): void
    {
        $start = (new Period('2022-01-01', '2022-01-10', excludeBoundaries: 'start'))->start();

        $this->assertInstanceOf(
            DateTime::class,
            $start
        );

        $this->assertSame(
            '2022-01-01T00:00:00.000+00:00',
            $start->toIsoString()
        );
    }

    public function testIteration(): void
    {
        $period = new Period('2022-01-01', '2022-01-02', 'hour');

        $dates = [];
        foreach ($period AS $date) {
            $this->assertInstanceOf(
                DateTime::class,
                $date
            );
    
            $dates[] = $date->toIsoString();
        }

        $this->assertSame(
            [
                '2022-01-01T00:00:00.000+00:00',
                '2022-01-01T01:00:00.000+00:00',
                '2022-01-01T02:00:00.000+00:00',
                '2022-01-01T03:00:00.000+00:00',
                '2022-01-01T04:00:00.000+00:00',
                '2022-01-01T05:00:00.000+00:00',
                '2022-01-01T06:00:00.000+00:00',
                '2022-01-01T07:00:00.000+00:00',
                '2022-01-01T08:00:00.000+00:00',
                '2022-01-01T09:00:00.000+00:00',
                '2022-01-01T10:00:00.000+00:00',
                '2022-01-01T11:00:00.000+00:00',
                '2022-01-01T12:00:00.000+00:00',
                '2022-01-01T13:00:00.000+00:00',
                '2022-01-01T14:00:00.000+00:00',
                '2022-01-01T15:00:00.000+00:00',
                '2022-01-01T16:00:00.000+00:00',
                '2022-01-01T17:00:00.000+00:00',
                '2022-01-01T18:00:00.000+00:00',
                '2022-01-01T19:00:00.000+00:00',
                '2022-01-01T20:00:00.000+00:00',
                '2022-01-01T21:00:00.000+00:00',
                '2022-01-01T22:00:00.000+00:00',
                '2022-01-01T23:00:00.000+00:00',
                '2022-01-02T00:00:00.000+00:00'
            ],
            $dates
        );
    }

    public function testIterationExcludeBoth(): void
    {
        $period = new Period('2022-01-01', '2022-01-02', 'hour', 'both');

        $dates = [];
        foreach ($period AS $date) {
            $this->assertInstanceOf(
                DateTime::class,
                $date
            );
    
            $dates[] = $date->toIsoString();
        }

        $this->assertSame(
            [
                '2022-01-01T01:00:00.000+00:00',
                '2022-01-01T02:00:00.000+00:00',
                '2022-01-01T03:00:00.000+00:00',
                '2022-01-01T04:00:00.000+00:00',
                '2022-01-01T05:00:00.000+00:00',
                '2022-01-01T06:00:00.000+00:00',
                '2022-01-01T07:00:00.000+00:00',
                '2022-01-01T08:00:00.000+00:00',
                '2022-01-01T09:00:00.000+00:00',
                '2022-01-01T10:00:00.000+00:00',
                '2022-01-01T11:00:00.000+00:00',
                '2022-01-01T12:00:00.000+00:00',
                '2022-01-01T13:00:00.000+00:00',
                '2022-01-01T14:00:00.000+00:00',
                '2022-01-01T15:00:00.000+00:00',
                '2022-01-01T16:00:00.000+00:00',
                '2022-01-01T17:00:00.000+00:00',
                '2022-01-01T18:00:00.000+00:00',
                '2022-01-01T19:00:00.000+00:00',
                '2022-01-01T20:00:00.000+00:00',
                '2022-01-01T21:00:00.000+00:00',
                '2022-01-01T22:00:00.000+00:00',
                '2022-01-01T23:00:00.000+00:00'
            ],
            $dates
        );
    }

}
