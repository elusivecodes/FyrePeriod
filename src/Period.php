<?php
declare(strict_types=1);

namespace Fyre\Period;

use Countable;
use Fyre\DateTime\DateTime;
use Fyre\Period\Traits\PeriodComparisonTrait;
use Fyre\Period\Traits\PeriodIterableTrait;
use Fyre\Period\Traits\PeriodOperationsTrait;
use Fyre\Period\Traits\PeriodStaticTrait;
use InvalidArgumentException;
use Iterator;
use RuntimeException;

use function array_key_exists;
use function in_array;
use function strtolower;

/**
 * Period
 */
class Period implements Countable, Iterator
{

    protected const GRANULARITIES = [
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second'
    ];

    protected const BOUNDARIES = [
        'both' => [false, false],
        'start' => [false, true],
        'end' => [true, false],
        'none' => [true, true]
    ];

    protected DateTime $start;

    protected DateTime $end;

    protected string|null $granularity;

    protected bool $includesStart;

    protected bool $includesEnd;

    use PeriodComparisonTrait;
    use PeriodIterableTrait;
    use PeriodOperationsTrait;
    use PeriodStaticTrait;

    /**
     * New Period constructor.
     * @param DateTime|null $start The start date.
     * @param DateTime|null $end The end date.
     * @param array $options The Period options.
     * @throws InvalidArgumentException if the granularity or boundaries are not valid.
     * @throws RuntimeException If the end date is before the start date.
     */
    public function __construct(DateTime|string $start, DateTime|string $end, array $options = [])
    {
        $this->start = static::createDate($start);
        $this->end = static::createDate($end);

        $granularity = strtolower($options['granularity'] ?? 'day');
        $excludeBoundaries = strtolower($options['excludeBoundaries'] ?? 'none');

        if (!in_array($granularity, static::GRANULARITIES)) {
            throw new InvalidArgumentException('Invalid granularity: '.$granularity);
        }

        if (!array_key_exists($excludeBoundaries, static::BOUNDARIES)) {
            throw new InvalidArgumentException('Invalid boundaries: '.$excludeBoundaries);
        }

        $this->granularity = $granularity;

        [$includesStart, $includesEnd] = static::BOUNDARIES[$excludeBoundaries];
        $this->includesStart = $includesStart;
        $this->includesEnd = $includesEnd;

        $this->includedStart = $this->includesStart ?
            $this->start :
            $this->start->add(1, $this->granularity);

        $this->includedEnd = $this->includesEnd ?
            $this->end :
            $this->end->sub(1, $this->granularity);

        if ($this->includedEnd->isBefore($this->includedStart, $this->granularity)) {
            throw new RuntimeException('The end date must be after the start date');
        }
    }

    /**
     * Get the length of the period.
     * @return int The length of the period.
     */
    public function length(): int
    {
        return $this->includedEnd->diff($this->includedStart, $this->granularity);
    }

    /**
     * Get the end date.
     * @return DateTime The end date
     */
    public function end(): DateTime
    {
        return $this->end;
    }

    /**
     * Get the granularity.
     * @return string The granularity.
     */
    public function granularity(): string
    {
        return $this->granularity;
    }

    /**
     * Get the included end date.
     * @return DateTime The included end date
     */
    public function includedEnd(): DateTime
    {
        return $this->includedEnd;
    }

    /**
     * Get the included start date.
     * @return DateTime The included start date
     */
    public function includedStart(): DateTime
    {
        return $this->includedStart;
    }

    /**
     * Determine if the Period includes the end date.
     * @return bool TRUE if the Period includes the end date, otherwise FALSE.
     */
    public function includesEnd(): bool
    {
        return $this->includesEnd;
    }

    /**
     * Determine if the Period includes the start date.
     * @return bool TRUE if the Period includes the start date, otherwise FALSE.
     */
    public function includesStart(): bool
    {
        return $this->includesStart;
    }

    /**
     * Get the start date.
     * @return DateTime The start date
     */
    public function start(): DateTime
    {
        return $this->start;
    }

}
