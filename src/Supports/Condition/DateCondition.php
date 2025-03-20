<?php

namespace Hanafalah\ModuleTransaction\Supports\Condition;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\Contracts\Voucher\DateCondition as DateConditionInterface;
use Illuminate\Support\Str;

class DateCondition extends Condition implements DateConditionInterface
{
    public function check(Model $voucher_rule, array $attributes): bool
    {
        $now      = now()->format('Y-m-d');
        $dates    = $this->mustArray($voucher_rule->rule['valid_until'] ?? $voucher_rule->rule['valid_date']);
        $dates[1] ??= $dates[0];
        switch (Str::snake($voucher_rule->condition)) {
            case 'in_date_range':
                $result = $now >= $dates[0] && $now <= $dates[1];
                break;
            case 'less_than_date':
                $result = $now < $dates[0];
                break;
            case 'after_than_date':
                $result = $now > $dates[0];
                break;
            default:
                throw new \Exception('Condition not found in Date Condition');
        }
        return $result;
    }
}
