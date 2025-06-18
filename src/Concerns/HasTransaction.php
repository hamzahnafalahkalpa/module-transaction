<?php

namespace Hanafalah\ModuleTransaction\Concerns;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModulePayment\Contracts\Data\JournalEntryData;

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
        static::updated(function($query){
            if (
                method_exists($query, 'isHasJournalEntry') && 
                $query->isHasJournalEntry() &&
                $query->isDirty('reported_at') &&
                isset($query->reported_at)
            ){
                $transaction = $query->transaction;
                $reference = app(config('database.models.'.$transaction->reference_type))->find($transaction->reference_id);

                app(config('app.contracts.JournalEntry'))->prepareStoreJournalEntry(
                    HasRequestData::newStatic()->requestDTO(JournalEntryData::class,[
                        'transaction_id' => $transaction->getKey(),
                        'reference_type' => $transaction->reference_type,
                        'reference_id'   => $transaction->reference_id,
                        'name'           => $reference->name ?? null
                    ])
                );
            }
        });
    }

    public function transaction()
    {
        return $this->morphOneModel('Transaction', 'reference');
    }

    public function reporting()
    {
        $transaction = $this->transaction()->firstOrCreate();
        $transaction->reported_at = now();
        $transaction->save();
    }

    public function canceling()
    {
        $transaction = $this->transaction()->firstOrCreate();
        $transaction->canceled_at = now();
        $transaction->save();
    }
}
