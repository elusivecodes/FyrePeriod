<?php
declare(strict_types=1);

namespace Fyre\Period;

use ArrayAccess;
use Countable;
use Fyre\Utility\Traits\MacroTrait;
use Iterator;

use function array_filter;
use function array_key_exists;
use function array_slice;
use function count;
use function usort;

use const ARRAY_FILTER_USE_BOTH;

/**
 * PeriodCollection
 */
class PeriodCollection implements ArrayAccess, Countable, Iterator
{
    use MacroTrait;

    protected int $index = 0;

    protected array $periods;

    /**
     * New PeriodCollection constructor.
     *
     * @param Period ...$periods The periods.
     */
    public function __construct(Period ...$periods)
    {
        $this->periods = $periods;
    }

    /**
     * Add periods to the collection.
     *
     * @param Period ...$periods The periods to add.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function add(Period ...$periods): static
    {
        return new static(...$this->periods, ...$periods);
    }

    /**
     * Get the boundaries of the collection.
     *
     * @return Period|null A new Period.
     */
    public function boundaries(): Period|null
    {
        if ($this->periods === []) {
            return null;
        }

        $firstPeriod = $this->periods[0];
        $lastPeriod = $this->periods[0];
        foreach ($this as $period) {
            if (!$firstPeriod || $period->includedStart()->isBefore($firstPeriod->includedStart())) {
                $firstPeriod = $period;
            }

            if (!$lastPeriod || $period->includedEnd()->isAfter($lastPeriod->includedEnd())) {
                $lastPeriod = $period;
            }
        }

        return new Period(
            $firstPeriod->start(),
            $lastPeriod->end(),
            $firstPeriod->granularity(),
            Period::getBoundaries($firstPeriod->includesStart(), $lastPeriod->includesEnd())
        );
    }

    /**
     * Get the Period count.
     *
     * @return int The Period count.
     */
    public function count(): int
    {
        return count($this->periods);
    }

    /**
     * Get the Period at the current index.
     *
     * @return Period The Period at the current index.
     */
    public function current(): Period
    {
        return $this->periods[$this->index];
    }

    /**
     * Get the the gaps between the periods in the collection.
     *
     * @return PeriodCollection A new PeriodCollection.
     */
    public function gaps(): static
    {
        if ($this->periods === []) {
            return new static();
        }

        return $this->boundaries()->subtractAll(...$this->periods);
    }

    /**
     * Intersect a period with every period in the collection.
     *
     * @param Period $period The Period to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function intersect(Period $other): static
    {
        $intersected = new static();

        foreach ($this as $period) {
            $overlap = $other->overlap($period);

            if (!$overlap) {
                continue;
            }

            $intersected[] = $overlap;
        }

        return $intersected;
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
     * Progress the index.
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Determine if an index exists.
     *
     * @param mixed $index The index.
     * @return bool TRUE if the index is set, otherwise FALSE.
     */
    public function offsetExists(mixed $index): bool
    {
        return array_key_exists($index, $this->periods);
    }

    /**
     * Get the Period at an index.
     *
     * @param mixed $index The index.
     * @return Period|null The Period at an index.
     */
    public function offsetGet(mixed $index): Period|null
    {
        return $this->periods[$index] ?? null;
    }

    /**
     * Set the Period at an index.
     *
     * @param mixed $index The index.
     * @param mixed $value The Period.
     */
    public function offsetSet(mixed $index, mixed $value): void
    {
        if ($index === null) {
            $this->periods[] = $value;
        } else {
            $this->periods[$index] = $value;
        }
    }

    /**
     * Unset an index.
     *
     * @param mixed $index The index.
     */
    public function offsetUnset(mixed $index): void
    {
        unset($this->periods[$index]);
    }

    /**
     * Get the overlap of all the collections.
     *
     * @param PeriodCollection $others The collections to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function overlapAll(PeriodCollection ...$others): static
    {
        $overlap = clone $this;

        foreach ($others as $other) {
            $overlap = $overlap->overlap($other);
        }

        return $overlap;
    }

    /**
     * Reset the index.
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Sort the periods.
     *
     * @return PeriodCollection A new PeriodCollection.
     */
    public function sort(): static
    {
        $periods = $this->periods;

        usort(
            $periods,
            static fn(Period $a, Period $b) => $a->includedStart()->getTimestamp() <=> $b->includedStart()->getTimestamp()
        );

        return new static(...$periods);
    }

    /**
     * Get the inverse overlap of the collections.
     *
     * @param PeriodCollection $others The collection to remove.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function subtract(PeriodCollection $others): static
    {
        if ($others->count() === 0) {
            return clone $this;
        }

        $collection = new static();

        foreach ($this as $period) {
            $subtracted = $period->subtractAll(...$others);
            $collection = $collection->add(...$subtracted);
        }

        return $collection;
    }

    /**
     * Filter the periods to remove duplicates.
     *
     * @return PeriodCollection A new PeriodCollection.
     */
    public function unique(): static
    {
        $periods = array_filter(
            $this->periods,
            function(Period $period, int $index): bool {
                $others = array_slice($this->periods, 0, $index);

                foreach ($others as $other) {
                    if ($period->equals($other)) {
                        return false;
                    }
                }

                return true;
            },
            ARRAY_FILTER_USE_BOTH
        );

        return new static(...$periods);
    }

    /**
     * Determine if the current index is valid.
     *
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return array_key_exists($this->index, $this->periods);
    }

    /**
     * Get the overlap of the collections.
     *
     * @param PeriodCollection $others The PeriodCollection to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    protected function overlap(PeriodCollection $others): static
    {
        if ($others->count() === 0) {
            return new static();
        }

        $collection = new static();

        foreach ($this as $period) {
            $overlaps = $period->overlapAny(...$others);
            $collection = $collection->add(...$overlaps);
        }

        return $collection;
    }
}
