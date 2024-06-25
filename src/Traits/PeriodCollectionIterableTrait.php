<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use Fyre\Period\Period;

use function array_key_exists;
use function count;

/**
 * PeriodCollectionIterableTrait
 */
trait PeriodCollectionIterableTrait
{
    protected int $index = 0;

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
        return array_key_exists($this->index, $this->periods);
    }
}
