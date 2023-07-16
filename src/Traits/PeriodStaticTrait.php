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

}
