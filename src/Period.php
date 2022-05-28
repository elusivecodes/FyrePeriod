<?php
declare(strict_types=1);

namespace Fyre\Period;

use
    Countable,
    Fyre\DateTime\DateTimeImmutable,
    Fyre\DateTime\DateTimeInterface,
    Fyre\Period\Traits\PeriodComparisonTrait,
    Fyre\Period\Traits\PeriodIterableTrait,
    Fyre\Period\Traits\PeriodOperationsTrait,
    Fyre\Period\Traits\PeriodStaticTrait,
    InvalidArgumentException,
    Iterator,
    RuntimeException;

use function
    array_key_exists,
    in_array,
    strtolower;

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

    protected DateTimeImmutable $start;

    protected DateTimeImmutable $end;

    protected string|null $granularity;

    protected bool $includesStart;

    protected bool $includesEnd;

    use
        PeriodComparisonTrait,
        PeriodIterableTrait,
        PeriodOperationsTrait,
        PeriodStaticTrait;

    /**
     * New Period constructor.
     * @param DateTimeInterface|null $start The start date.
     * @param DateTimeInterface|null $end The end date.
     * @param array $options The Period options.
     * @throws InvalidArgumentException if the granularity or boundaries are not valid.
     * @throws RuntimeException If the end date is before the start date.
     */
    public function __construct(DateTimeInterface|string $start, DateTimeInterface|string $end, array $options = [])
    {
        $this->start = static::createImmutableDate($start);
        $this->end = static::createImmutableDate($end);

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
     * @return DateTimeImmutable The end date
     */
    public function end(): DateTimeImmutable
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
     * @return DateTimeImmutable The included end date
     */
    public function includedEnd(): DateTimeImmutable
    {
        return $this->includedEnd;
    }

    /**
     * Get the included start date.
     * @return DateTimeImmutable The included start date
     */
    public function includedStart(): DateTimeImmutable
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
     * @return DateTimeImmutable The start date
     */
    public function start(): DateTimeImmutable
    {
        return $this->start;
    }

}
