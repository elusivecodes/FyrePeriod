<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use Fyre\DateTime\DateTime;
use Fyre\Period\Period;

/**
 * PeriodComparisonTrait
 */
trait PeriodComparisonTrait
{
    /**
     * Determine if this period contains another Period.
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
     * Determine if this period ends on a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on a given date, otherwise FALSE.
     */
    public function endEquals(DateTime $date): bool
    {
        return static::isSame($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine if this period ends after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends after a given date, otherwise FALSE.
     */
    public function endsAfter(DateTime $date): bool
    {
        return static::isAfter($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine if this period ends on or after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on or after a given date, otherwise FALSE.
     */
    public function endsAfterOrEquals(DateTime $date): bool
    {
        return static::isSameOrAfter($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine if this period ends before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends before a given date, otherwise FALSE.
     */
    public function endsBefore(DateTime $date): bool
    {
        return static::isBefore($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine if this period ends on or before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on or before a given date, otherwise FALSE.
     */
    public function endsBeforeOrEquals(DateTime $date): bool
    {
        return static::isSameOrBefore($this->includedEnd, $date, $this->granularity);
    }

    /**
     * Determine if this period equals another Period.
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
     * Determine if this period includes a given date.
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
     * Determine if this period overlaps with another Period.
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
     * Determine if this period starts on a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on a given date, otherwise FALSE.
     */
    public function startEquals(DateTime $date): bool
    {
        return static::isSame($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine if this period starts after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts after a given date, otherwise FALSE.
     */
    public function startsAfter(DateTime $date): bool
    {
        return static::isAfter($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine if this period starts on or after a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on or after a given date, otherwise FALSE.
     */
    public function startsAfterOrEquals(DateTime $date): bool
    {
        return static::isSameOrAfter($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine if this period starts before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts before a given date, otherwise FALSE.
     */
    public function startsBefore(DateTime $date): bool
    {
        return static::isBefore($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine if this period starts on or before a given date.
     *
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on or before a given date, otherwise FALSE.
     */
    public function startsBeforeOrEquals(DateTime $date): bool
    {
        return static::isSameOrBefore($this->includedStart, $date, $this->granularity);
    }

    /**
     * Determine if this period touches another Period.
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
}
