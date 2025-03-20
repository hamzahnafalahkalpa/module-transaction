<?php

namespace Zahzah\ModuleTransaction\Concerns;

trait PaymentCalculation{
    public function rounding(int $value,int $roll_rounding = 100): int{
        return $value;
        // return round($value/$roll_rounding) * $roll_rounding;
    }

    protected function hasPaymentDetails(array $attributes): bool{
        return isset($attributes['payment_details']) && count($attributes['payment_details']) > 0;
    }
}