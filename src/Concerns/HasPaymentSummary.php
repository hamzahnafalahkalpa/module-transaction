<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasPaymentSummary
{
    use HasTransaction;

    public static function bootHasPaymentSummary()
    {
        static::created(function ($model) {
            if (\method_exists($model, 'transaction')) {
                $transaction = $model->transaction()->firstOrCreate();
                $transaction_id = $transaction->getKey();
            }
            $model->paymentSummary()->firstOrCreate([
                'transaction_id' => $transaction_id ?? null,
                'reference_id'   => $model->getKey(),
                'reference_type' => $model->getMorphClass()
            ]);
        });
    }

    public function paymentSummary()
    {
        return $this->morphOneModel('PaymentSummary', 'reference');
    }

    public function paymentSummaries()
    {
        return $this->morphManyModel('PaymentSummary', 'reference');
    }
}
