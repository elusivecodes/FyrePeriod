<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use
    Fyre\Period\Period,
    Fyre\Period\PeriodCollection;

/**
 * PeriodOperationsTrait
 */
trait PeriodOperationsTrait
{

    /**
     * Get the symmetric difference between the periods.
     * @param Period $other The Period to compare against.
     * @return PeriodCollection The symmetric difference.
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
     * @param Period $other The Period to compare against.
     * @return Period|null The gap.
     */
    public function gap(Period $other): static|null
    {
        static::checkGranularity($this, $other);

        if ($this->overlapsWith($other) || $this->touches($other)) {
            return null;
        }

        if ($this->includedStart->isAfter($other->includedEnd())) {
            return new static($other->end(), $this->start, [
                'granularity' => $this->granularity,
                'excludeBoundaries' => static::getBoundaries(!$other->includesEnd(), !$this->includesStart)
            ]);
        }

        return new static($this->end, $other->start(), [
            'granularity' => $this->granularity,
            'excludeBoundaries' => static::getBoundaries(!$this->includesEnd, !$other->includesStart())
        ]);
    }

    /**
     * Get the overlap of the periods.
     * @param Period $other The Period to compare against.
     * @return Period|null The overlap.
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

        return new static($startPeriod->start(), $endPeriod->end(), [
            'granularity' => $this->granularity,
            'excludeBoundaries' => static::getBoundaries($startPeriod->includesStart(), $endPeriod->includesEnd())
        ]);
    }

    /**
     * Get the overlap of all the periods.
     * @param Period ...$others The periods to compare against.
     * @return Period|null The overlap.
     */
    public function overlapAll(Period ...$others): static|null
    {
        $overlap = new static($this->start, $this->end, [
            'granularity' => $this->granularity,
            'excludeBoundaries' => static::getBoundaries($this->includesStart, $this->includesEnd)
        ]);

        foreach ($others AS $other) {
            $overlap = $overlap->overlap($other);

            if ($overlap === null) {
                return null;
            }
        }

        return $overlap;
    }

    /**
     * Get the overlaps of any of the periods.
     * @param Period ...$others The periods to compare against.
     * @return PeriodCollection|null The overlaps.
     */
    public function overlapAny(Period ...$others): PeriodCollection
    {
        $overlaps = [];

        foreach ($others AS $other) {
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
     * @return Period The next Period.
     */
    public function renew(): static
    {
        $diff = $this->end->diff($this->start, $this->granularity);

        return new static($this->end, $this->end->add($diff, $this->granularity), [
            'granularity' => $this->granularity,
            'excludeBoundaries' => static::getBoundaries($this->includesStart, $this->includesEnd)
        ]);
    }

    /**
     * Get the inverse overlap of the periods.
     * @param Period $other The period to remove.
     * @return PeriodCollection The inverse overlap.
     */
    public function subtract(Period $other): PeriodCollection
    {
        static::checkGranularity($this, $other);

        if (!$this->overlapsWith($other)) {
            return new PeriodCollection($this);
        }

        $subtractions = [];

        if ($this->includedStart->isBefore($other->includedStart())) {
            $subtractions[] = new static($this->start, $other->start(), [
                'granularity' => $this->granularity,
                'excludeBoundaries' => static::getBoundaries($this->includesStart, !$other->includesStart())
            ]);
        }

        if ($this->includedEnd->isAfter($other->includedEnd())) {
            $subtractions[] = new static($other->end(), $this->end, [
                'granularity' => $this->granularity,
                'excludeBoundaries' => static::getBoundaries(!$other->includesEnd(), $this->includesEnd)
            ]);
        }

        return new PeriodCollection(...$subtractions);
    }

    /**
     * Get the inverse overlap of all periods.
     * @param Period ...$others The periods to compare against.
     * @return PeriodCollection The inverse overlap.
     */
    public function subtractAll(Period ...$others): PeriodCollection
    {
        $subtractions = [];

        foreach ($others AS $other) {
            $subtractions[] = $this->subtract($other);
        }

        return (new PeriodCollection($this))->overlapAll(...$subtractions);
    }

}
