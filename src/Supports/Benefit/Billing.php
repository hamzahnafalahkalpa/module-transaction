<?php

namespace Hanafalah\ModuleTransaction\Supports\Benefit;

use Hanafalah\ModuleTransaction\Contracts\Supports\Benefit\Billing as BillingInterface;

class Billing extends Benefit implements BillingInterface
{
    public function calculating(&$payment_history, &$voucher, &$attributes)
    {
        $this->initializeBenefit($voucher);
        $this->calculateBenefitByTypeValue($attributes, $payment_history);
        return $payment_history;
    }
}
