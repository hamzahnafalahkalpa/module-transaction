<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionData;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Transaction as ContractsTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends PackageManagement implements ContractsTransaction
{
    protected string $__entity = 'Transaction';
    public $transaction_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'transaction',
            'tags'     => ['transaction', 'transaction-index'],
            'duration' => 60 * 12
        ],
        'show' => [
            'name'     => 'transaction',
            'tags'     => ['transaction', 'transaction-show'],
            'duration' => 60 * 12
        ]
    ];

    public function prepareStoreTransaction(TransactionData $transaction_dto): Model{
        $reference_type   = $transaction_dto->reference_type;
        $reference_schema = config('module-transaction.transaction_types.'.Str::snake($reference_type).'.schema');        
        if (isset($reference_schema) && isset($transaction_dto->reference)) {
            $schema_reference          = $this->schemaContract(Str::studly($reference_schema));
            $reference                 = $schema_reference->prepareStore($transaction_dto->reference);
            $transaction_dto->reference_id = $reference->getKey();
            $transaction_dto->props['prop_'.Str::snake($transaction_dto->reference_type)] = $reference->toViewApi()->resolve();
        }

        $add = [
            'parent_id' => $transaction_dto->parent_id,
        ];
        if (isset($transaction_dto->id)){
            $guard = ['id' => $transaction_dto->id];
        }else{
            $guard = [
                'reference_type' => $transaction_dto->reference_type,
                'reference_id' => $transaction_dto->reference_id
            ];
        }
        $transaction = $this->usingEntity()->firstOrCreate($guard,$add);
        if (isset($transaction_dto->consument) && config('module-transaction.consument') !== null){
            $consument = $this->schemaContract('consument')->prepareStoreConsument($transaction_dto->consument);
            $this->TransactionHasConsumentModel()->updateOrCreate([
                'transaction_id' => $transaction->getKey(),
                'consument_id'   => $consument->getKey()
            ]);
            $transaction_dto->props['prop_consument'] = $consument->toViewApi()->resolve();
        }

        if (isset($transaction_dto->transaction_items) && count($transaction_dto->transaction_items) > 0){
            foreach ($transaction_dto->transaction_items as $transaction_item_dto) {
                $transaction_item_dto->transaction_id = $transaction->getKey();
                $transaction_item_dto->reference_type = $transaction->reference_type;
                $transaction_item_dto->reference_id = $transaction->reference_id;
                $this->schemaContract('transaction_item')->prepareStoreTransactionItem($transaction_item_dto);
            }
        }

        if (isset($transaction_dto->payment_details) && count($transaction_dto->payment_details) > 0){
            foreach ($transaction_dto->payment_details as $payment_detail_dto) {
                $payment_detail_dto->transaction_id = $transaction->getKey();
                $this->schemaContract('payment_detail')->prepareStorePaymentDetail($payment_detail_dto);
            }
        }

        $this->fillingProps($transaction, $transaction_dto->props);
        $transaction->save();
        return $this->transaction_model = $transaction;
    }

    public function camelEntity(): string{
        return 'trx';
    }

    public function trx(mixed $conditionals = null): Builder{
        return $this->generalSchemaModel($conditionals)->when(isset(request()->reference_type),function($query){
            return $query->where('reference_type',request()->reference_type);
        });
    }
}
