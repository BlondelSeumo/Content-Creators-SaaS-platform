<?php

namespace App\Admin\Dashboard\Metrics;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class Value extends Metrics
{
    /**
     * The available functions
     * @var array
     */
    private $functions = [
        'count', 'min', 'max', 'sum', 'avg'
    ];

    /**
     * The available ranges
     * @var array
     */
    public $ranges = [
        1, 3, 5, 7, 10, 14, 21, 30, 60, 90
    ];

    /**
     * @param $model
     * @param $function
     * @param $range
     * @param null $column
     * @param null $dateColumn
     * @return array
     */
    public function get($model, $function, $range, $column = null, $dateColumn = null)
    {
        if (!in_array($function, $this->functions)) {
            return $this->error('Invalid function provided');
        }

        if (!in_array($range, $this->ranges)) {
            return $this->error('Invalid range provided');
        }

        return $this->aggregate($model, $function, $range, $column, $dateColumn);
    }

    /**
     * Calculate the current range
     *
     * @param $range
     * @return array
     */
    protected function calcRange($range)
    {
        return [
            now()->subDays($range),
            now()
        ];
    }

    /**
     * Calculate the previous range
     *
     * @param $range
     * @return array
     */
    protected function calcPreviousRange($range)
    {
        return [
            now()->subDays($range * 2),
            now()->subDays($range)
        ];
    }

    /**
     * @param $model
     * @param $function
     * @param $range
     * @param null $column
     * @param null $dateColumn
     * @return array
     */
    protected function aggregate($model, $function, $range, $column = null, $dateColumn = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $column = $column ?? $query->getModel()->getQualifiedKeyName();
        $dateColumn = $dateColumn ?? $query->getModel()->getCreatedAtColumn();

        $value = (clone $query)->whereBetween($dateColumn, $this->calcRange($range))
            ->{$function}($column);

        $previousValue = (clone $query)->whereBetween($dateColumn, $this->calcPreviousRange($range))
            ->{$function}($column);

        $current = round($value, 0);
        $previous = round($previousValue, 0);

        return [
            'value' => $current,
            'previous_value' => $previous,
            'ranges' => $this->ranges,
            'growth' => $this->calcGrowth($previous, $current)
        ];
    }
}