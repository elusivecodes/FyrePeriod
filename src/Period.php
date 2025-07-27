<?php
declare(strict_types=1);

namespace Fyre\Period;

use Countable;
use Fyre\DateTime\DateTime;
use Fyre\Utility\Traits\MacroTrait;
use InvalidArgumentException;
use Iterator;
use RuntimeException;

use function array_key_exists;
use function in_array;
use function is_string;
use function strtolower;

/**
 * Period
 */
class Period implements Countable, Iterator
{
    use MacroTrait;

    protected const BOUNDARIES = [
        'both' => [false, false],
        'start' => [false, true],
        'end' => [true, false],
        'none' => [true, true],
    ];

    protected const GRANULARITIES = [
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second',
    ];

    protected DateTime $end;

    protected string|null $granularity;

    protected DateTime $includedEnd;

    protected DateTime $includedStart;

    protected bool $includesEnd;

    protected bool $includesStart;

    protected int $index = 0;

    protected DateTime $start;

    /**
     * Get the boundary string.
     *
     * @param bool $includesStart Whether the Period includes the start.
     * @param bool $includesEnd Whether the Period includes the end.
     * @return string The boundary string.
     */
    public static function getBoundaries(bool $includesStart, bool $includesEnd): string
    {
        if (!$includesStart && !$includesEnd) {
            return 'both';
        }

        if (!$includesStart) {
            return 'start';
        }

        if (!$includesEnd) {
            return 'end';
        }

        return 'none';
    }

    /**
     * New Period constructor.
     *
     * @param DateTime|null $start The start date.
     * @param DateTime|null $end The end date.
     * @param string $granularity The granularity.
     * @param string $excludeBoundaries The boundaries to exclude.
     *
     * @throws InvalidArgumentException if the granularity or boundaries are not valid.
     * @throws RuntimeException If the end date is before the start date.
     */
    public function __construct(DateTime|string $start, DateTime|string $end, string $granularity = 'day', string $excludeBoundaries = 'none')
    {
        $this->start = static::createDate($start);
        $this->end = static::createDate($end);

        $granularity = strtolower($granularity);
        $excludeBoundaries = strtolower($excludeBoundaries);

        if (!in_array($granularity, static::GRANULARITIES)) {
            throw new InvalidArgumentException('Invalid granularity: '.$granularity);
        }

        if (!array_key_exists($excludeBoundaries, static::BOUNDARIES)) {
            throw new InvalidArgumentException('Invalid boundaries: '.$excludeBoundaries);
        }

        $this->granularity = $granularity;

        [$includesStart, $includesEnd] = static::BOUNDARIES[$excludeBoundaries];
        $this->includesStart = $includesStart;
        $this->includesEnd = $includesEnd;

        $this->includedStart = $this->includesStart ?
            $this->start :
            static::add($this->start, 1, $this->granularity);

        $this->includedEnd = $this->includesEnd ?
            $this->end :
            static::sub($this->end, 1, $this->granularity);

        if (static::isBefore($this->includedEnd, $this->includedStart, $this->granularity)) {
            throw new RuntimeException('The end date must be after the start date');
        }
    }

    /**
     * Determine whether this period contains another Period.
     *
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period contains the other Period, otherwise FALSE.
     */
    public function contains(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return static::isSameOrBefore($this->includedStart, $other->includedStart(), $this->granularity) &&
            static::isSameOrAfter($this->includedEnd, $other->includedEnd(), $this->granularity);
    }

    /**
     * Get the period length.
     *
     * @return int The period length.
     */
    public function count(): int
    {
        return static::diff($this->includedEnd, $this->includedStart, $this->granularity) + 1;
    }

    /**
     * Get the date at the current index.
     *
     * @return DateTime The date at the current index.
     */
    public function current(): DateTime
    {
        return static::add($this->includedStart, $this->index, $this->granularity);
    }

    /**
     * Get the symmetric difference between the periods.
     *
     * @param Period $other The Period to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function diffSymmetric(Period $other): PeriodCollection
    {
        $collection = new PeriodCollection($this, $other);
        $overlap = $this->overlap($other);

        if (!$overlap) {
            return $collection;
        }

        return $collection->boundaries()->subtract($overlap);
    }

    /**
     * Get the end date.
     *
     * @return DateTime The end date
     */
    public function end(): DateTime
    {
        return $this->end;
    }

    /**
     * Determine whether this period ends on a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on a given date, otherwise FALSE.
     */
    public function endEquals(DateTime $date): bool
    {
        return static::isSame($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine whether this period ends after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends after a given date, otherwise FALSE.
     */
    public function endsAfter(DateTime $date): bool
    {
        return static::isAfter($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine whether this period ends on or after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on or after a given date, otherwise FALSE.
     */
    public function endsAfterOrEquals(DateTime $date): bool
    {
        return static::isSameOrAfter($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine whether this period ends before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends before a given date, otherwise FALSE.
     */
    public function endsBefore(DateTime $date): bool
    {
        return static::isBefore($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine whether this period ends on or before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on or before a given date, otherwise FALSE.
     */
    public function endsBeforeOrEquals(DateTime $date): bool
    {
        return static::isSameOrBefore($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine whether this period equals another Period.
     *
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period equals the other Period, otherwise FALSE.
     */
    public function equals(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return static::isSame($this->includedStart, $other->includedStart(), $this->granularity) &&
            static::isSame($this->includedEnd, $other->includedEnd(), $this->granularity);
    }

    /**
     * Get the gap between the periods.
     *
     * @param Period $other The Period to compare against.
     * @return Period|null A new Period.
     */
    public function gap(Period $other): static|null
    {
        static::checkGranularity($this, $other);

        if ($this->overlapsWith($other) || $this->touches($other)) {
            return null;
        }

        if ($this->includedStart->isAfter($other->includedEnd())) {
            return new static(
                $other->end(),
                $this->start,
                $this->granularity,
                static::getBoundaries(!$other->includesEnd(), !$this->includesStart)
            );
        }

        return new static(
            $this->end,
            $other->start(),
            $this->granularity,
            static::getBoundaries(!$this->includesEnd, !$other->includesStart())
        );
    }

    /**
     * Get the granularity.
     *
     * @return string The granularity.
     */
    public function granularity(): string
    {
        return $this->granularity;
    }

    /**
     * Get the included end date.
     *
     * @return DateTime The included end date
     */
    public function includedEnd(): DateTime
    {
        return $this->includedEnd;
    }

    /**
     * Get the included start date.
     *
     * @return DateTime The included start date
     */
    public function includedStart(): DateTime
    {
        return $this->includedStart;
    }

    /**
     * Determine whether this period includes a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period includes a given date, otherwise FALSE.
     */
    public function includes(DateTime $date): bool
    {
        return static::isSameOrBefore($this->includedStart, $date, $this->granularity) &&
            static::isSameOrAfter($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine whether the Period includes the end date.
     *
     * @return bool TRUE if the Period includes the end date, otherwise FALSE.
     */
    public function includesEnd(): bool
    {
        return $this->includesEnd;
    }

    /**
     * Determine whether the Period includes the start date.
     *
     * @return bool TRUE if the Period includes the start date, otherwise FALSE.
     */
    public function includesStart(): bool
    {
        return $this->includesStart;
    }

    /**
     * Get the current index.
     *
     * @return int The current index.
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * Get the length of the period.
     *
     * @return int The length of the period.
     */
    public function length(): int
    {
        return static::diff($this->includedEnd, $this->includedStart, $this->granularity);
    }

    /**
     * Progress the index.
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Get the overlap of the periods.
     *
     * @param Period $other The Period to compare against.
     * @return Period|null A new Period.
     */
    public function overlap(Period $other): static|null
    {
        static::checkGranularity($this, $other);

        $startPeriod = $this->includedStart->isAfter($other->includedStart()) ?
            $this : $other;

        $endPeriod = $this->includedEnd->isBefore($other->includedEnd()) ?
            $this : $other;

        if ($startPeriod->includedStart->isAfter($endPeriod->includedEnd())) {
            return null;
        }

        return new static(
            $startPeriod->start(),
            $endPeriod->end(),
            $this->granularity,
            static::getBoundaries($startPeriod->includesStart(), $endPeriod->includesEnd())
        );
    }

    /**
     * Get the overlap of all the periods.
     *
     * @param Period ...$others The periods to compare against.
     * @return Period|null A new Period.
     */
    public function overlapAll(Period ...$others): static|null
    {
        $overlap = new static(
            $this->start,
            $this->end,
            $this->granularity,
            static::getBoundaries($this->includesStart, $this->includesEnd)
        );

        foreach ($others as $other) {
            $overlap = $overlap->overlap($other);

            if ($overlap === null) {
                return null;
            }
        }

        return $overlap;
    }

    /**
     * Get the overlaps of any of the periods.
     *
     * @param Period ...$others The periods to compare against.
     * @return PeriodCollection|null A new PeriodCollection.
     */
    public function overlapAny(Period ...$others): PeriodCollection
    {
        $overlaps = [];

        foreach ($others as $other) {
            $overlap = $this->overlap($other);

            if ($overlap === null) {
                continue;
            }

            $overlaps[] = $overlap;
        }

        return new PeriodCollection(...$overlaps);
    }

    /**
     * Determine whether this period overlaps with another Period.
     *
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period overlaps with the other Period, otherwise FALSE.
     */
    public function overlapsWith(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return static::isSameOrBefore($this->includedStart, $other->includedEnd(), $this->granularity) &&
            static::isSameOrAfter($this->includedEnd, $other->includedStart(), $this->granularity);
    }

    /**
     * Create a new period with the same length after this period.
     *
     * @return Period A new Period.
     */
    public function renew(): static
    {
        $diff = static::diff($this->end, $this->start, $this->granularity);

        return new static(
            $this->end,
            static::add($this->end, $diff, $this->granularity),
            $this->granularity,
            static::getBoundaries($this->includesStart, $this->includesEnd)
        );
    }

    /**
     * Reset the index.
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Get the start date.
     *
     * @return DateTime The start date
     */
    public function start(): DateTime
    {
        return $this->start;
    }

    /**
     * Determine whether this period starts on a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on a given date, otherwise FALSE.
     */
    public function startEquals(DateTime $date): bool
    {
        return static::isSame($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine whether this period starts after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts after a given date, otherwise FALSE.
     */
    public function startsAfter(DateTime $date): bool
    {
        return static::isAfter($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine whether this period starts on or after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on or after a given date, otherwise FALSE.
     */
    public function startsAfterOrEquals(DateTime $date): bool
    {
        return static::isSameOrAfter($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine whether this period starts before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts before a given date, otherwise FALSE.
     */
    public function startsBefore(DateTime $date): bool
    {
        return static::isBefore($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine whether this period starts on or before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on or before a given date, otherwise FALSE.
     */
    public function startsBeforeOrEquals(DateTime $date): bool
    {
        return static::isSameOrBefore($this->includedStart, $date, $this->granularity);
    }

    /**
     * Get the inverse overlap of the periods.
     *
     * @param Period $other The period to remove.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function subtract(Period $other): PeriodCollection
    {
        static::checkGranularity($this, $other);

        if (!$this->overlapsWith($other)) {
            return new PeriodCollection($this);
        }

        $subtractions = [];

        if ($this->includedStart->isBefore($other->includedStart())) {
            $subtractions[] = new static(
                $this->start,
                $other->start(),
                $this->granularity,
                static::getBoundaries($this->includesStart, !$other->includesStart())
            );
        }

        if ($this->includedEnd->isAfter($other->includedEnd())) {
            $subtractions[] = new static(
                $other->end(),
                $this->end,
                $this->granularity,
                static::getBoundaries(!$other->includesEnd(), $this->includesEnd)
            );
        }

        return new PeriodCollection(...$subtractions);
    }

    /**
     * Get the inverse overlap of all periods.
     *
     * @param Period ...$others The periods to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function subtractAll(Period ...$others): PeriodCollection
    {
        $subtractions = [];

        foreach ($others as $other) {
            $subtractions[] = $this->subtract($other);
        }

        return (new PeriodCollection($this))->overlapAll(...$subtractions);
    }

    /**
     * Determine whether this period touches another Period.
     *
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period touches the other Period, otherwise FALSE.
     */
    public function touches(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return static::isSame($this->includedStart, $other->includedEnd(), $this->granularity) ||
            static::isSame($this->includedEnd, $other->includedStart(), $this->granularity);
    }

    /**
     * Determine whether the current index is valid.
     *
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return $this->index < $this->count();
    }

    /**
     * Add an amount of time to a date (by granularity).
     *
     * @param DateTime $date The DateTime.
     * @param int $amount The amount of time to add.
     * @param string|null $granularity The granularity.
     * @return DateTime The new DateTime.
     */
    protected static function add(DateTime $date, int $amount, string|null $granularity = null): DateTime
    {
        return match ($granularity) {
            'day' => $date->addDays($amount),
            'hour' => $date->addHours($amount),
            'minute' => $date->addMinutes($amount),
            'month' => $date->addMonths($amount),
            'second' => $date->addSeconds($amount),
            'year' => $date->addYears($amount)
        };
    }

    /**
     * Check granularity of two periods.
     *
     * @param Period $a The first Period.
     * @param Period $b The second Period.
     *
     * @throws RuntimeException if the granularity doesn't match.
     */
    protected static function checkGranularity(Period $a, Period $b): void
    {
        if ($a->granularity() === $b->granularity()) {
            return;
        }

        throw new RuntimeException('Period granularities do not match');
    }

    /**
     * Create a DateTime.
     *
     * @param DateTime|string $date The input date.
     * @return DateTime The DateTime.
     */
    protected static function createDate(DateTime|string $date): DateTime
    {
        if (is_string($date)) {
            return new DateTime($date);
        }

        return $date;
    }

    /**
     * Get the difference between two dates (based on granularity).
     *
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return int The difference.
     */
    protected static function diff(DateTime $a, DateTime $b, string|null $granularity = null): int
    {
        return match ($granularity) {
            'day' => $a->diffInDays($b),
            'hour' => $a->diffInHours($b),
            'minute' => $a->diffInMinutes($b),
            'month' => $a->diffInMonths($b),
            'second' => $a->diffInSeconds($b),
            'year' => $a->diffInYears($b),
            default => $a->diff($b)
        };
    }

    /**
     * Determine whether a date is after another date (based on granularity).
     *
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is after the other date, otherwise FALSE.
     */
    protected static function isAfter(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isAfterDay($b),
            'hour' => $a->isAfterHour($b),
            'minute' => $a->isAfterMinute($b),
            'month' => $a->isAfterMonth($b),
            'second' => $a->isAfterSecond($b),
            'year' => $a->isAfterYear($b),
            default => $a->isAfter($b)
        };
    }

    /**
     * Determine whether a date is before another date (based on granularity).
     *
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is before the other date, otherwise FALSE.
     */
    protected static function isBefore(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isBeforeDay($b),
            'hour' => $a->isBeforeHour($b),
            'minute' => $a->isBeforeMinute($b),
            'month' => $a->isBeforeMonth($b),
            'second' => $a->isBeforeSecond($b),
            'year' => $a->isBeforeYear($b),
            default => $a->isBefore($b)
        };
    }

    /**
     * Determine whether a date is the same as another date (based on granularity).
     *
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is the same as the other date, otherwise FALSE.
     */
    protected static function isSame(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isSameDay($b),
            'hour' => $a->isSameHour($b),
            'minute' => $a->isSameMinute($b),
            'month' => $a->isSameMonth($b),
            'second' => $a->isSameSecond($b),
            'year' => $a->isSameYear($b),
            default => $a->isSame($b)
        };
    }

    /**
     * Determine whether a date is the same as or after another date (based on granularity).
     *
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is the same as or after the other date, otherwise FALSE.
     */
    protected static function isSameOrAfter(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isSameOrAfterDay($b),
            'hour' => $a->isSameOrAfterHour($b),
            'minute' => $a->isSameOrAfterMinute($b),
            'month' => $a->isSameOrAfterMonth($b),
            'second' => $a->isSameOrAfterSecond($b),
            'year' => $a->isSameOrAfterYear($b),
            default => $a->isSameOrAfter($b)
        };
    }

    /**
     * Determine whether a date is the same as or before another date (based on granularity).
     *
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is the same as or before the other date, otherwise FALSE.
     */
    protected static function isSameOrBefore(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isSameOrBeforeDay($b),
            'hour' => $a->isSameOrBeforeHour($b),
            'minute' => $a->isSameOrBeforeMinute($b),
            'month' => $a->isSameOrBeforeMonth($b),
            'second' => $a->isSameOrBeforeSecond($b),
            'year' => $a->isSameOrBeforeYear($b),
            default => $a->isSameOrBefore($b)
        };
    }

    /**
     * Subtract an amount of time from a date (by granularity).
     *
     * @param DateTime $date The DateTime.
     * @param int $amount The amount of time to subtract.
     * @param string|null $granularity The granularity.
     * @return DateTime The new DateTime.
     */
    protected static function sub(DateTime $date, int $amount, string|null $granularity = null): DateTime
    {
        return match ($granularity) {
            'day' => $date->subDays($amount),
            'hour' => $date->subHours($amount),
            'minute' => $date->subMinutes($amount),
            'month' => $date->subMonths($amount),
            'second' => $date->subSeconds($amount),
            'year' => $date->subYears($amount)
        };
    }
}
