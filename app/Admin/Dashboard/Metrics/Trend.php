<?php

namespace App\Admin\Dashboard\Metrics;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Trend extends Metrics
{
    /**
     * The available functions
     */
    private $functions = [
        'count', 'min', 'max', 'sum', 'avg'
    ];

    /**
     * The available unit types, the format and the allowed ranges
     * @var array
     */
    public $units = [
        'day' => [
            'format' => null,
            'ranges' => [
                3, 5, 7, 10, 14, 21, 30, 60, 90, 180, 365
            ]
        ],

        'month' => [
            'format' => null,
            'ranges' => [
                2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 24, 36, 48
            ]
        ]
    ];

    public function __construct()
    {
        $this->units['day']['format'] = __('Y-m-d');
        $this->units['month']['format'] = __('Y-m');
    }

    /**
     * @param $model
     * @param $function
     * @param $unit
     * @param $range
     * @param null $column
     * @param null $dateColumn
     * @return array
     */
    public function get($model, $function, $unit, $range, $column = null, $dateColumn = null)
    {
        if (!array_key_exists($unit, $this->units)) {
            return $this->error('Invalid unit provided');
        }

        if (!in_array($function, $this->functions)) {
            return $this->error('Invalid function provided');
        }

        if (!in_array($range, $this->units[$unit]['ranges'])) {
            return $this->error('Invalid range provided');
        }

        return $this->aggregate($model, $function, $range, $unit, $column, $dateColumn);
    }

    /**
     * @param $range
     * @param $unit
     * @return array
     */
    protected function calcRange($range, $unit)
    {
        switch ($unit) {
            case 'month':
                return [
                    now()->subMonths($range-1)->firstOfMonth()->setTime(0, 0),
                    now()
                ];
            case 'day':
                return [
                    now()->subDays($range-1)->setTime(0, 0),
                    now()
                ];
        }
    }

    /**
     * @param $range
     * @param $unit
     * @return mixed
     */
    protected function calcAllDates($range, $unit)
    {
        $nextDate = $range[0]->copy();
        $endingDate = $range[1]->copy();

        $possibleDateResults[$nextDate->copy()->format($this->units[$unit]['format'])] = 0;

        while ($nextDate->lt($endingDate)) {
            if ($unit == 'month') {
                $nextDate = $nextDate->addMonths(1);
            } elseif ($unit == 'day') {
                $nextDate = $nextDate->addDays(1);
            }

            if ($nextDate->lte($endingDate)) {
                $possibleDateResults[$nextDate->copy()->format($this->units[$unit]['format'])] = 0;
            }
        }

        return $possibleDateResults;
    }

    /**
     * @param $model
     * @param $function
     * @param $range
     * @param $unit
     * @param $column
     * @param null $dateColumn
     * @return array
     */
    protected function aggregate($model, $function, $range, $unit, $column, $dateColumn = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $date = $this->calcRange($range, $unit);

        $wrappedDateColumn = (new TrendDateExpression($query, $dateColumn, $unit))->get();
        $wrappedColumn = $query->getQuery()->getGrammar()->wrap($column);

        $results = $query->select([
            DB::raw("{$wrappedDateColumn} as date_result"),
            DB::raw("{$function}({$wrappedColumn}) as aggregate")
        ])
            ->whereBetween($dateColumn, $date)
            ->groupBy(DB::raw("{$wrappedDateColumn}"))
            ->orderBy('date_result')
            ->get();

        $results = array_merge($this->calcAllDates($date, $unit, $this->units[$unit]), $results->mapWithKeys(function ($result) use ($unit, $dateColumn) {
            return [Carbon::parse($result->date_result)->format($this->units[$unit]['format']) => round($result->aggregate, 0)];
        })->all());

        if (count($results) > $range) {
            array_shift($results);
        }

        return [
            'values' => $results,
            'ranges' => $this->units[$unit]['ranges'],
            'last' => last($results)
        ];
    }
}