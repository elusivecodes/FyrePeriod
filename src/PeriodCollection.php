<?php
declare(strict_types=1);

namespace Fyre\Period;

use
    ArrayAccess,
    Countable,
    Fyre\Period\Traits\PeriodCollectionIterableTrait,
    Fyre\Period\Traits\PeriodCollectionOperationsTrait,
    Iterator;

use const
    ARRAY_FILTER_USE_BOTH;

use function
    array_filter,
    array_slice,
    usort;

/**
 * PeriodCollection
 */
class PeriodCollection implements ArrayAccess, Countable, Iterator
{

    protected array $periods = [];

    use
        PeriodCollectionIterableTrait,
        PeriodCollectionOperationsTrait;

    /**
     * New PeriodCollection constructor.
     * @param Period ...$periods The periods.
     */
    public function __construct(Period ...$periods)
    {
        $this->periods = $periods;
    }

    /**
     * Add periods to the collection.
     * @param Period ...$periods The periods to add.
     * @return PeriodCollection The new periods.
     */
    public function add(Period ...$periods): static
    {
        return new static(...$this->periods, ...$periods);
    }

    /**
     * Get the boundaries of the collection.
     * @return Period|null The boundaries.
     */
    public function boundaries(): Period|null
    {
        if ($this->periods === []) {
            return null;
        }

        $firstPeriod = $this->periods[0];
        $lastPeriod = $this->periods[0];
        foreach ($this AS $period) {
            if (!$firstPeriod || $period->includedStart()->isBefore($firstPeriod->includedStart())) {
                $firstPeriod = $period;
            }

            if (!$lastPeriod || $period->includedEnd()->isAfter($lastPeriod->includedEnd())) {
                $lastPeriod = $period;
            }
        }

        return new Period($firstPeriod->start(), $lastPeriod->end(), [
            'granularity' => $firstPeriod->granularity(),
            'excludeBoundaries' => Period::getBoundaries($firstPeriod->includesStart(), $lastPeriod->includesEnd())
        ]);
    }

    /**
     * Sort the periods.
     * @return PeriodCollection The sorted periods.
     */
    public function sort(): static
    {
        $periods = $this->periods;

        usort($periods, fn(Period $a, Period $b) => $a->includedStart()->getTimestamp() <=> $b->includedStart()->getTimestamp());

        return new static(...$periods);
    }

    /**
     * Filter the periods to remove duplicates.
     * @return PeriodCollection The unique periods.
     */
    public function unique(): static
    {
        $periods = array_filter(
            $this->periods,
            function(Period $period, int $index): bool
            {
                $others = array_slice($this->periods, 0, $index);

                foreach ($others AS $other) {
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

}
