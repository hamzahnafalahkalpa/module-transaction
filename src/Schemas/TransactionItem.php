<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\ModuleTransaction\Contracts\Schemas\TransactionItem as ContractsTransacitonItem;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionItemData;

class TransactionItem extends PackageManagement implements ContractsTransacitonItem
{
    protected string $__entity = 'TransactionItem';
    public $transaction_item_model;

    public function prepareStoreTransactionItem(TransactionItemData $transaction_item_dto): Model{
        if (isset($transaction_item_dto->item)){
            $item_dto = &$transaction_item_dto->item;
            $item_type = $transaction_item_dto->item_type;
            $transaction_item = $this->schemaContract($item_type)->{'prepareStore'.$item_type}($item_dto);
            $transaction_item_dto->item_id = $transaction_item->getKey();
        }
        if (isset($transaction_item_dto->id)) {
            $guard = ['id' => $transaction_item_dto->id];
        } else {
            $guard = [
                'transaction_id' => $transaction_item_dto->transaction_id,
                'parent_id'      => $transaction_item_dto->parent_id ?? null,
                'item_type'      => $transaction_item_dto->item_type,
                'item_id'        => $transaction_item_dto->item_id,
                'reference_type' => $transaction_item_dto->reference_type,
                'reference_id'   => $transaction_item_dto->reference_id
            ];
        }
        $transaction_item = $this->usingEntity()->updateOrCreate($guard, [
            'name' => $transaction_item_dto->name
        ]);
        if (isset($transaction_item_dto->payment_detail) && config('module-transaction.payment_detail') !== null) {
            $payment_detail = &$transaction_item_dto->payment_detail;
            if (!isset($payment_detail->payment_summary_id)){
                $transaction_model = $transaction_item_dto->transaction_model ?? $transaction_item->transaction;
                $payment_summary = $transaction_model->paymentSummary;
                $payment_detail->payment_summary_id = $payment_summary->getKey();
            }
            $payment_detail->transaction_item_id = $transaction_item->getKey();
            $this->schemaContract('payment_detail')
                 ->prepareStorePaymentDetail($payment_detail);
        }
        return $this->transaction_item_model = $transaction_item;
    }
}
