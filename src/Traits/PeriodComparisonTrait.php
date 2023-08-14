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
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period contains the other Period, otherwise FALSE.
     */
    public function contains(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return $this->includedStart->isSameOrBefore($other->includedStart(), $this->granularity) &&
            $this->includedEnd->isSameOrAfter($other->includedEnd(), $this->granularity);
    }

    /**
     * Determine if this period ends on a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on a given date, otherwise FALSE.
     */
    public function endEquals(DateTime $date): bool
    {
        return $this->includedEnd->isSame($date, $this->granularity);
    }

    /**
     * Determine if this period ends after a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends after a given date, otherwise FALSE.
     */
    public function endsAfter(DateTime $date): bool
    {
        return $this->includedEnd->isAfter($date, $this->granularity);
    }

    /**
     * Determine if this period ends on or after a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on or after a given date, otherwise FALSE.
     */
    public function endsAfterOrEquals(DateTime $date): bool
    {
        return $this->includedEnd->isSameOrAfter($date, $this->granularity);
    }

    /**
     * Determine if this period ends before a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends before a given date, otherwise FALSE.
     */
    public function endsBefore(DateTime $date): bool
    {
        return $this->includedEnd->isBefore($date, $this->granularity);
    }

    /**
     * Determine if this period ends on or before a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period ends on or before a given date, otherwise FALSE.
     */
    public function endsBeforeOrEquals(DateTime $date): bool
    {
        return $this->includedEnd->isSameOrBefore($date, $this->granularity);
    }

    /**
     * Determine if this period equals another Period.
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period equals the other Period, otherwise FALSE.
     */
    public function equals(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return $this->includedStart->isSame($other->includedStart(), $this->granularity) &&
            $this->includedEnd->isSame($other->includedEnd(), $this->granularity);
    }

    /**
     * Determine if this period includes a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period includes a given date, otherwise FALSE.
     */
    public function includes(DateTime $date): bool
    {
        return $this->includedStart->isSameOrBefore($date, $this->granularity) &&
            $this->includedEnd->isSameOrAfter($date, $this->granularity);
    }

    /**
     * Determine if this period overlaps with another Period.
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period overlaps with the other Period, otherwise FALSE.
     */
    public function overlapsWith(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return $this->includedStart->isSameOrBefore($other->includedEnd(), $this->granularity) &&
            $this->includedEnd->isSameOrAfter($other->includedStart(), $this->granularity);
    }

    /**
     * Determine if this period starts on a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on a given date, otherwise FALSE.
     */
    public function startEquals(DateTime $date): bool
    {
        return $this->includedStart->isSame($date, $this->granularity);
    }

    /**
     * Determine if this period starts after a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts after a given date, otherwise FALSE.
     */
    public function startsAfter(DateTime $date): bool
    {
        return $this->includedStart->isAfter($date, $this->granularity);
    }

    /**
     * Determine if this period starts on or after a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on or after a given date, otherwise FALSE.
     */
    public function startsAfterOrEquals(DateTime $date): bool
    {
        return  $this->includedStart->isSameOrAfter($date, $this->granularity);
    }

    /**
     * Determine if this period starts before a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts before a given date, otherwise FALSE.
     */
    public function startsBefore(DateTime $date): bool
    {
        return $this->includedStart->isBefore($date, $this->granularity);
    }

    /**
     * Determine if this period starts on or before a given date.
     * @param DateTime $date The DateTime to compare against.
     * @return bool TRUE if the period starts on or before a given date, otherwise FALSE.
     */
    public function startsBeforeOrEquals(DateTime $date): bool
    {
        return $this->includedStart->isSameOrBefore($date, $this->granularity);
    }

    /**
     * Determine if this period touches another Period.
     * @param Period $other The Period to compare against.
     * @return bool TRUE if the period touches the other Period, otherwise FALSE.
     */
    public function touches(Period $other): bool
    {
        static::checkGranularity($this, $other);

        return $this->includedStart->isSame($other->includedEnd(), $this->granularity) ||
            $this->includedEnd->isSame($other->includedStart(), $this->granularity);
    }

}
