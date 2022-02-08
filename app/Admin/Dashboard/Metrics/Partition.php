<?php

namespace App\Admin\Dashboard\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Partition extends Metrics
{
    /**
     * The available functions
     * @var array
     */
    private $functions = [
        'count', 'min', 'max', 'sum', 'avg'
    ];

    /**
     * @param $model
     * @param $function
     * @param null $column
     * @param null $groupBy
     * @param null $callback
     * @return array
     */
    public function get($model, $function, $column = null, $groupBy = null, $callback = null)
    {
        if (!in_array($function, $this->functions)) {
            return $this->error('Invalid function provided');
        }

        return $this->aggregate($model, $function, $column, $groupBy, $callback);
    }

    /**
     * @param $result
     * @param $groupBy
     * @param $callback
     * @return array
     */
    protected function formatResult($result, $groupBy, $callback)
    {
        $key = $result->{last(explode('.', $groupBy))};

        return [$callback ? $callback($key) : $key => round($result->aggregate, 0)];
    }

    /**
     * @param $model
     * @param $function
     * @param $column
     * @param $groupBy
     * @param $callback
     * @return array
     */
    protected function aggregate($model, $function, $column, $groupBy, $callback)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        DB::enableQueryLog();
        $wrappedColumn = $query->getQuery()->getGrammar()->wrap(
            $column = $column ?? $query->getModel()->getQualifiedKeyName()
        );

        $results = $query->select(
            $groupBy, DB::raw("{$function}({$wrappedColumn}) as aggregate"))
            ->groupBy($groupBy)
            ->orderBy('aggregate', 'desc')
            ->get();
//
//        print '<pre>';
//        var_dump($wrappedColumn);
//        var_dump($groupBy);
//        var_dump($results);

        return [
            'values' => $results->mapWithKeys(function ($result) use ($groupBy, $callback) {
                return $this->formatResult($result, $groupBy, $callback);
            })->all()
        ];
    }
}