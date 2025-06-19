<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasTransaction
{
    public static function bootHasTransaction()
    {
        static::addGlobalScope('with_transaction', function ($query) {
            $query->with('transaction');
        });
        static::created(function ($query) {
            $query->transaction()->firstOrCreate();
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
