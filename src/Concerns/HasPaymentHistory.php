<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasPaymentHistory
{
    /**
     * Get the payment summary for the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function paymentHistory()
    {
        return $this->morphOneModel('PaymentHistory', 'reference');
    }

    public function paymentHistories()
    {
        return $this->morphManyModel('PaymentHistory', 'reference');
    }
}
