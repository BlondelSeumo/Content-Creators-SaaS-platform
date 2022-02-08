<?php

namespace App\Admin\Dashboard\Metrics;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class TrendDateExpression
{
    /**
     * @var Builder
     */
    public $query;

    /**
     * @var
     */
    public $column;

    /**
     * @var
     */
    public $unit;

    /**
     * TrendDateExpression constructor.
     * @param Builder $query
     * @param $column
     * @param $unit
     */
    public function __construct(Builder $query, $column, $unit)
    {
        $this->query = $query;
        $this->column = $column;
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function get()
    {
        switch ($this->query->getConnection()->getDriverName()) {
            case 'mysql':
            case 'mariadb':
                return $this->mysqlDateFormat();
            case 'sqlite':
                return $this->sqlliteDateFormat();
            case 'pgsql':
                return $this->postgreDateFormat();
            default:
                throw new InvalidArgumentException('This driver is not supported.');
        }
    }

    /**
     * @return string
     */
    private function mysqlDateFormat()
    {
        switch ($this->unit) {
            case 'month':
                return "date_format({$this->wrap($this->column)}, '%Y-%m')";
            case 'day':
                return "date_format({$this->wrap($this->column)}, '%Y-%m-%d')";
        }
    }

    /**
     * @return string
     */
    private function postgreDateFormat()
    {
        switch ($this->unit) {
            case 'month':
                return "to_char({$this->wrap($this->column)}, 'YYYY-MM')";
            case 'day':
                return "to_char({$this->wrap($this->column)}, 'YYYY-MM-DD')";
        }
    }

    /**
     * @return string
     */
    private function sqlliteDateFormat()
    {
        switch ($this->unit) {
            case 'month':
                return "strftime('%Y-%m', datetime({$this->wrap($this->column)}))";
            case 'day':
                return "strftime('%Y-%m-%d', datetime({$this->wrap($this->column)}))";
        }
    }

    /**
     * @param $value
     * @return string
     */
    private function wrap($value)
    {
        return $this->query->getQuery()->getGrammar()->wrap($value);
    }
}