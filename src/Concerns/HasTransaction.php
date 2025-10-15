<?php

namespace Hanafalah\ModuleTransaction\Concerns;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;

trait HasTransaction
{
    use HasRequestData;
    
    public static function bootHasTransaction()
    {
        static::created(function ($query) {
            $transaction = app(config('app.contracts.Transaction'))
            ->prepareStoreTransaction($query->requestDTO(config('app.contracts.TransactionData'),[
                'reference_model' => $query,
                'reference_id' => $query->getKey(),
                'reference_type' => $query->getMorphClass()
            ]));
            $query->prop_transaction = $transaction->toViewApi()->resolve();
        });
    }

    public function transaction(){
        return $this->morphOneModel('Transaction', 'reference');
    }

    public function reporting(){
        $transaction = $this->transaction()->firstOrCreate();
        $transaction->reported_at = now();
        $transaction->save();
    }

    public function journalReporting(){
        $transaction = $this->transaction()->firstOrCreate();
        $transaction->journal_reported_at = now();
        $transaction->save();
    }

    public function canceling(){
        $transaction = $this->transaction()->firstOrCreate();
        $transaction->canceled_at = now();
        $transaction->save();
    }
}
