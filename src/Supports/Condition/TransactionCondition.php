<?php

namespace Hanafalah\ModuleTransaction\Supports\Condition;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\Contracts\Supports\Condition\TransactionCondition as TransactionConditionInterface;
use Illuminate\Support\Str;

class TransactionCondition extends Condition implements TransactionConditionInterface
{
    public function check(Model $voucher_rule, array $attributes): bool
    {
        $total = static::$__payment_history->total_debt;
        switch (Str::snake($voucher_rule->condition)) {
            case 'minimum_transaction':
                return $total >= $voucher_rule->rule['value'];
                break;
            default:
                throw new \Exception('Condition not found in Transaction Condition');
        }
        return false;
    }
}
