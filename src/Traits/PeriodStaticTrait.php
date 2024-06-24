<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use Fyre\DateTime\DateTime;
use Fyre\Period\Period;
use RuntimeException;

use function is_string;

/**
 * PeriodStaticTrait
 */
trait PeriodStaticTrait
{
    /**
     * Get the boundary string.
     * @param bool $includesStart Whether the Period includes the start.
     * @param bool $includesEnd Whether the Period includes the end.
     * @return string The boundary string.
     */
    public static function getBoundaries(bool $includesStart, bool $includesEnd): string
    {
        if (!$includesStart && !$includesEnd) {
            return 'both';
        }

        if (!$includesStart) {
            return 'start';
        }

        if (!$includesEnd) {
            return 'end';
        }

        return 'none';
    }

    /**
     * Add an amount of time to a date (by granularity).
     * @param DateTime $date The DateTime.
     * @param int $amount The amount of time to add.
     * @param string|null $granularity The granularity.
     * @return DateTime The new DateTime.
     */
    protected static function add(DateTime $date, int $amount, string|null $granularity = null): DateTime
    {
        return match ($granularity) {
            'day' => $date->addDays($amount),
            'hour' => $date->addHours($amount),
            'minute' => $date->addMinutes($amount),
            'month' => $date->addMonths($amount),
            'second' => $date->addSeconds($amount),
            'year' => $date->addYears($amount)
        };
    }

    /**
     * Check granularity of two periods.
     * @param Period $a The first Period.
     * @param Period $b The second Period.
     * @throws RuntimeException if the granularity doesn't match.
     */
    protected static function checkGranularity(Period $a, Period $b): void
    {
        if ($a->granularity() === $b->granularity()) {
            return;
        }

        throw new RuntimeException('Period granularities do not match');
    }

    /**
     * Create a DateTime.
     * @param DateTime|string $date The input date.
     * @return DateTime The DateTime.
     */
    protected static function createDate(DateTime|string $date): DateTime
    {
        if (is_string($date)) {
            return new DateTime($date);
        }

        return $date;
    }

    /**
     * Get the difference between two dates (based on granularity).
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return int The difference.
     */
    protected static function diff(DateTime $a, DateTime $b, string|null $granularity = null): int
    {
        return match ($granularity) {
            'day' => $a->diffInDays($b),
            'hour' => $a->diffInHours($b),
            'minute' => $a->diffInMinutes($b),
            'month' => $a->diffInMonths($b),
            'second' => $a->diffInSeconds($b),
            'year' => $a->diffInYears($b),
            default => $a->diff($b)
        };
    }

    /**
     * Determine if a date is after another date (based on granularity).
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is after the other date, otherwise FALSE.
     */
    protected static function isAfter(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isAfterDay($b),
            'hour' => $a->isAfterHour($b),
            'minute' => $a->isAfterMinute($b),
            'month' => $a->isAfterMonth($b),
            'second' => $a->isAfterSecond($b),
            'year' => $a->isAfterYear($b),
            default => $a->isAfter($b)
        };
    }

    /**
     * Determine if a date is before another date (based on granularity).
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is before the other date, otherwise FALSE.
     */
    protected static function isBefore(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isBeforeDay($b),
            'hour' => $a->isBeforeHour($b),
            'minute' => $a->isBeforeMinute($b),
            'month' => $a->isBeforeMonth($b),
            'second' => $a->isBeforeSecond($b),
            'year' => $a->isBeforeYear($b),
            default => $a->isBefore($b)
        };
    }

    /**
     * Determine if a date is the same as another date (based on granularity).
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is the same as the other date, otherwise FALSE.
     */
    protected static function isSame(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isSameDay($b),
            'hour' => $a->isSameHour($b),
            'minute' => $a->isSameMinute($b),
            'month' => $a->isSameMonth($b),
            'second' => $a->isSameSecond($b),
            'year' => $a->isSameYear($b),
            default => $a->isSame($b)
        };
    }

    /**
     * Determine if a date is the same as or after another date (based on granularity).
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is the same as or after the other date, otherwise FALSE.
     */
    protected static function isSameOrAfter(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isSameOrAfterDay($b),
            'hour' => $a->isSameOrAfterHour($b),
            'minute' => $a->isSameOrAfterMinute($b),
            'month' => $a->isSameOrAfterMonth($b),
            'second' => $a->isSameOrAfterSecond($b),
            'year' => $a->isSameOrAfterYear($b),
            default => $a->isSameOrAfter($b)
        };
    }

    /**
     * Determine if a date is the same as or before another date (based on granularity).
     * @param DateTime $a The first date.
     * @param DateTime $b The second date.
     * @param string|null $granularity The granularity.
     * @return bool TRUE if the date is the same as or before the other date, otherwise FALSE.
     */
    protected static function isSameOrBefore(DateTime $a, DateTime $b, string|null $granularity = null): bool
    {
        return match ($granularity) {
            'day' => $a->isSameOrBeforeDay($b),
            'hour' => $a->isSameOrBeforeHour($b),
            'minute' => $a->isSameOrBeforeMinute($b),
            'month' => $a->isSameOrBeforeMonth($b),
            'second' => $a->isSameOrBeforeSecond($b),
            'year' => $a->isSameOrBeforeYear($b),
            default => $a->isSameOrBefore($b)
        };
    }

    /**
     * Subtract an amount of time from a date (by granularity).
     * @param DateTime $date The DateTime.
     * @param int $amount The amount of time to subtract.
     * @param string|null $granularity The granularity.
     * @return DateTime The new DateTime.
     */
    protected static function sub(DateTime $date, int $amount, string|null $granularity = null): DateTime
    {
        return match ($granularity) {
            'day' => $date->subDays($amount),
            'hour' => $date->subHours($amount),
            'minute' => $date->subMinutes($amount),
            'month' => $date->subMonths($amount),
            'second' => $date->subSeconds($amount),
            'year' => $date->subYears($amount)
        };
    }
}
