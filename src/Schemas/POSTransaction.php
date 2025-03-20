<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\Contracts\POSTransaction as ContractsPOSTransaction;

class POSTransaction extends Transaction implements ContractsPOSTransaction
{
    public static $billing_model;

    protected function showUsingRelation(): array
    {
        return [
            "reference",
            "paymentSummary" => function ($query) {
                $query->debtNotZero()->with('recursiveChilds');
            },
            "billing" => function ($query) {
                $query->with('splitBills.paymentHistory')->whereNull('reported_at');
            }
        ];
    }

    public function prepareCheckout(?array $attributes = null): Model
    {
        $attributes ??= request()->all();
        if (!isset($attributes['uuid'])) throw new \Exception('uuid is required');
        $transaction = $this->TransactionModel()->where('uuid', $attributes['uuid'])->firstOrFail();

        if (!isset($transaction->split_bills) || count($transaction->split_bills) == 0) throw new \Exception('The transaction has not been billed');

        $billing = $this->schemaContract('billing')->prepareStoreBilling([
            'transaction_id' => $transaction->getKey(),
            'cashier_id'     => $attributes['cashier_id'],
            'cashier_type'   => $attributes['cashier_type'],
            'author_type'    => $attributes['author_type'] ?? null,
            'author_id'      => $attributes['author_id'] ?? null,
            'split_bills'    => $transaction->split_bills,
            'reported_at'    => now()
        ]);
        return static::$billing_model = $billing;
    }

    public function checkout(): array
    {
        return $this->transaction(function () {
            return $this->schemaContract('billing')
                ->checkoutBilling($this->prepareCheckout());
        });
    }

    public function prepareStorePaidPayment(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (!isset($attributes['uuid'])) throw new \Exception('uuid is required');
        $transaction  = $this->TransactionModel()->where('uuid', $attributes['uuid'])->firstOrFail();
        if (!isset($attributes['split_bills']) || count($attributes['split_bills']) == 0) throw new \Exception('split_bills is required');
        $billing = $this->schemaContract('billing')->prepareStoreBilling([
            'transaction'    => $transaction,
            'transaction_id' => $transaction->getKey(),
            'cashier_id'     => $attributes['cashier_id'],
            'cashier_type'   => $attributes['cashier_type'],
            'author_type'    => $attributes['author_type'] ?? null,
            'author_id'      => $attributes['author_id'] ?? null,
            'split_bills'    => $attributes['split_bills'] ?? $attributes['split_bill'] ?? []
        ]);
        return static::$billing_model = $billing;
    }

    public function storePaidPayment(): array
    {
        return $this->transaction(function () {
            return $this->schemaContract('billing')
                ->showBilling($this->prepareStorePaidPayment());
        });
    }

    protected function getTransactionBuilder($morphs)
    {
        $status = $this->getTransactionStatus();
        $morphs = $this->mustArray($morphs);
        return $this->trx(function ($query) use ($morphs) {
            $query->when(isset($morphs), function ($query) use ($morphs) {
                $query->whereIn('reference_type', $morphs);
            });
        })->with('reference')
            ->whereHasMorph('reference', $this->VisitPatientModel()->getMorphClass(), function ($query) {
                $query->whereHas('patient');
            })->whereHas("paymentSummary", fn($q) => $q->debtNotZero())
            ->whereIn('status', [$status['DRAFT'], $status['ACTIVE']]);
    }
}
