<?php
declare(strict_types=1);

namespace Fyre\Period;

use ArrayAccess;
use Countable;
use Fyre\Period\Traits\PeriodCollectionIterableTrait;
use Fyre\Period\Traits\PeriodCollectionOperationsTrait;
use Iterator;

use function array_filter;
use function array_slice;
use function usort;

use const ARRAY_FILTER_USE_BOTH;

/**
 * PeriodCollection
 */
class PeriodCollection implements ArrayAccess, Countable, Iterator
{
    use PeriodCollectionIterableTrait;
    use PeriodCollectionOperationsTrait;

    protected array $periods = [];

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
     * @return PeriodCollection A new PeriodCollection.
     */
    public function add(Period ...$periods): static
    {
        return new static(...$this->periods, ...$periods);
    }

    /**
     * Get the boundaries of the collection.
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
     * Sort the periods.
     * @return PeriodCollection A new PeriodCollection.
     */
    public function sort(): static
    {
        $periods = $this->periods;

        usort($periods, fn(Period $a, Period $b) => $a->includedStart()->getTimestamp() <=> $b->includedStart()->getTimestamp());

        return new static(...$periods);
    }

    /**
     * Filter the periods to remove duplicates.
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
}
