<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use Fyre\DateTime\DateTime;

/**
 * PeriodIterableTrait
 */
trait PeriodIterableTrait
{
    protected int $index = 0;

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
     * Reset the index.
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Determine if the current index is valid.
     *
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return $this->index < $this->count();
    }
}
