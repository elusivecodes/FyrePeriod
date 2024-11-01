# FyrePeriod

**FyrePeriod** is a free, open-source date period library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Periods](#periods)
- [Period Collections](#period-collections)



## Installation

**Using Composer**

```
composer require fyre/period
```


## Periods

```php
use Fyre\Period\Period;
```

- `$start` is a [*DateTime*](https://github.com/elusivecodes/FyreDateTime) or string representing the start date.
- `$end` is a [*DateTime*](https://github.com/elusivecodes/FyreDateTime) or string representing the end date.
- `$granularity` is a string representing the granularity, and can be one of either "*year*", "*month*", "*day*", "*hour*", "*minute*" or "*second*", and will default to "*day*".
- `$excludeBoundaries` is a string representing the excluded boundaries, and can be one of either "*none*", "*start*", "*end*" or "*both*", and will default to "*none*".

```php
$period = new Period($start, $end, $granularity, $excludeBoundaries);
```

The *Period* is an implementation of an *Iterator* and can be used in a foreach loop.

```php
foreach ($period AS $date) { }
```

**Contains**

Determine whether this period contains another *Period*.

- `$other` is the *Period* to compare against.

```php
$contains = $period->contains($other);
```

**Diff Symmetric**

Get the symmetric difference between the periods.

- `$other` is the *Period* to compare against.

```php
$diffSymmetric = $period->diffSymmetric($other);
```

This method will return a new [*PeriodCollection*](#period-collections).

**End**

Get the end date.

```php
$end = $period->end();
```

This method will return a [*DateTime*](https://github.com/elusivecodes/FyreDateTime).

**End Equals**

Determine whether this period ends on a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$endEquals = $period->endEquals($date);
```

**Ends After**

Determine whether this period ends after a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$endsAfter = $period->endsAfter($date);
```

**Ends After Or Equals**

Determine whether this period ends on or after a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$endsAfterOrEquals = $period->endsAfterOrEquals($date);
```

**Ends Before**

Determine whether this period ends before a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$endsBefore = $period->endsBefore($date);
```

**Ends Before Or Equals**

Determine whether this period ends on or before a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$endsBeforeOrEquals = $period->endsBeforeOrEquals($date);
```

**Equals**

Determine whether this period equals another Period.

- `$other` is the *Period* to compare against.

```php
$equals = $period->equals($other);
```

**Gap**

Get the gap between the periods.

- `$other` is the *Period* to compare against.

```php
$gap = $period->gap($other);
```

This method will return a new *Period*, or *null* if there's no gap.

**Granularity**

Get the granularity.

```php
$granularity = $period->granularity();
```

**Included End**

Get the included end date.

```php
$includedEnd = $period->includedEnd();
```

This method will return a [*DateTime*](https://github.com/elusivecodes/FyreDateTime).

**Included Start**

Get the included start date.

```php
$includedStart = $period->includedStart();
```

This method will return a [*DateTime*](https://github.com/elusivecodes/FyreDateTime).

**Includes End**

Determine whether the Period includes the end date.

```php
$includesEnd = $period->includesEnd();
```

**Includes**

Determine whether this period includes a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$includes = $period->includes($date);
```

**Includes Start**

Determine whether the Period includes the start date.

```php
$includesStart = $period->includesStart();
```

**Length**

Get the length of the period.

```php
$length = $period->length();
```

**Overlap**

Get the overlap of the periods.

- `$other` is the *Period* to compare against.

```php
$overlap = $period->overlap($other);
```

This method will return a new *Period*, or *null* if there's no overlap.

**Overlap All**

Get the overlap of all the periods.

```php
$overlapAll = $period->overlapAll(...$others);
```

This method will return a new *Period*, or *null* if there's no overlap.

**Overlap Any**

Get the overlaps of any of the periods.

```php
$overlapAny = $period->overlapAny(...$others);
```

This method will return a new [*PeriodCollection*](#period-collections).

**Overlaps With**

Determine whether this period overlaps with another Period.

- `$other` is the *Period* to compare against.

```php
$overlapsWith = $period->overlapsWith($other);
```

**Renew**

Create a new period with the same length after this period.

```php
$renewed = $period->renew();
```

This method will return a new *Period*.

**Start**

Get the start date.

```php
$start = $period->start();
```

This method will return a [*DateTime*](https://github.com/elusivecodes/FyreDateTime).

**Start Equals**

Determine whether this period starts on a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$startEquals = $period->startEquals($date);
```

**Starts After**

Determine whether this period starts after a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$startsAfter = $period->startsAfter($date);
```

**Starts After Or Equals**

Determine whether this period starts on or after a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$startsAfterOrEquals = $period->startsAfterOrEquals($date);
```

**Starts Before**

Determine whether this period starts before a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$startsBefore = $period->startsBefore($date);
```

**Starts Before Or Equals**

Determine whether this period starts on or before a given date.

- `$date` is the [*DateTime*](https://github.com/elusivecodes/FyreDateTime) to compare against.

```php
$startsBeforeOrEquals = $period->startsBeforeOrEquals($date);
```

**Subtract**

Get the inverse overlap of the periods.

- `$other` is the *Period* to compare against.

```php
$subtract = $period->subtract($other);
```

This method will return a new [*PeriodCollection*](#period-collections).

**Subtract All**

Get the inverse overlap of all periods.

```php
$subtractAll = $period->subtractAll(...$others);
```

This method will return a new [*PeriodCollection*](#period-collections).

**Touches**

Determine whether this period touches another Period.

- `$other` is the *Period* to compare against.

```php
$touches = $period->touches($other);
```


## Period Collections

```php
use Fyre\Period\PeriodCollection;
```

All arguments supplied will be used as periods for the collection.

```php
$periodCollection = new PeriodCollection(...$periods);
```

The *PeriodCollection* is an implementation of an *Iterator* and can be used in a foreach loop.

```php
foreach ($periodCollection AS $period) { }
```

**Add**

Add periods to the collection.

All arguments supplied will be used as periods to add to the collection.

```php
$added = $periodCollection->add(...$periods);
```

This method will return a new *PeriodCollection*.

**Boundaries**

Get the boundaries of the collection.

```php
$boundaries = $periodCollection->boundaries();
```

This method will return a new [*Period*](#periods), or *null* if the collection is empty.

**Gaps**

Get the the gaps between the periods in the collection.

```php
$gaps = $periodCollection->gaps();
```

This method will return a new *PeriodCollection*.

**Intersect**

Intersect a period with every period in the collection.

- `$period` is the [*Period*](#periods) to compare against.

```php
$intersect = $periodCollection->intersect($period);
```

This method will return a new *PeriodCollection*.

**Overlap All**

Get the overlap of all the collections.

All arguments supplied will be used as collections to compare against.

```php
$overlapAll = $periodCollection->overlapAll(...$others);
```

This method will return a new *PeriodCollection*.

**Sort**

Sort the periods.

```php
$sorted = $periodCollection->sort();
```

This method will return a new *PeriodCollection*.

**Subtract**

Get the inverse overlap of the collections.

- `$others` is the *PeriodCollection* to compare against.

```php
$subtract = $periodCollection->subtract($others);
```

This method will return a new *PeriodCollection*.

**Unique**

Filter the periods to remove duplicates.

```php
$unique = $periodCollection->unique();
```

This method will return a new *PeriodCollection*.