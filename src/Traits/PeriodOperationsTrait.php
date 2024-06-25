<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use Fyre\Period\Period;
use Fyre\Period\PeriodCollection;

/**
 * PeriodOperationsTrait
 */
trait PeriodOperationsTrait
{
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
}
