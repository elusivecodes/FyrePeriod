<?php
declare(strict_types=1);

namespace Fyre\Period\Traits;

use
    Fyre\DateTime\DateTime,
    Fyre\DateTime\DateTimeImmutable,
    Fyre\DateTime\DateTimeInterface,
    Fyre\Period\Period,
    RuntimeException;

use function
    is_string;

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
     * Create a DateTimeImmutable.
     * @param DateTimeInterface|string $date The input date.
     * @return DateTimeImmutable The DateTimeImmutable.
     */
    protected static function createImmutableDate(DateTimeInterface|string $date): DateTimeImmutable
    {
        if (is_string($date)) {
            return new DateTimeImmutable($date);
        }

        if ($date instanceof DateTimeImmtutable) {
            return $date;
        }

        return DateTimeImmutable::fromTimestamp($date->getTimestamp(), $date->getTimeZone(), $date->getLocale());
    }

}
