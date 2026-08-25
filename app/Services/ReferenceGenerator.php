<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ReferenceGenerator
{
    public static function generate(string $prefix, Model $model, string $column = 'reference'): string
    {
        $date = now()->format('Ymd');
        $pattern = $prefix.'-'.$date.'-%';

        $last = $model->newQuery()
            ->where($column, 'like', $pattern)
            ->orderByDesc($column)
            ->value($column);

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
