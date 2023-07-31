<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use Fyre\Period\Period;
use Fyre\Period\PeriodCollection;

/**
 * PeriodCollectionOperationsTrait
 */
trait PeriodCollectionOperationsTrait
{

    /**
     * Get the the gaps between the periods in the collection.
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
     * @param Period $period The Period to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function intersect(Period $other): static
    {
        $intersected = new static();

        foreach ($this AS $period) {
            $overlap = $other->overlap($period);

            if (!$overlap) {
                continue;
            }

            $intersected[] = $overlap;
        }

        return $intersected;
    }

    /**
     * Get the overlap of all the collections.
     * @param PeriodCollection $others The collections to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function overlapAll(PeriodCollection ...$others): static
    {
        $overlap = clone $this;

        foreach ($others AS $other) {
            $overlap = $overlap->overlap($other);
        }

        return $overlap;
    }

    /**
     * Get the inverse overlap of the collections.
     * @param PeriodCollection $others The collection to remove.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function subtract(PeriodCollection $others): static
    {
        if ($others->count() === 0) {
            return clone $this;
        }

        $collection = new static();

        foreach ($this AS $period) {
            $subtracted = $period->subtractAll(...$others);
            $collection = $collection->add(...$subtracted);
        }

        return $collection;
    }

    /**
     * Get the overlap of the collections.
     * @param PeriodCollection $others The PeriodCollection to compare against.
     * @return PeriodCollection A new PeriodCollection.
     */
    protected function overlap(PeriodCollection $others): static
    {
        if ($others->count() === 0) {
            return new static();
        }

        $collection = new static();

        foreach ($this AS $period) {
            $overlaps = $period->overlapAny(...$others);
            $collection = $collection->add(...$overlaps);
        }

        return $collection;
    }

}
