<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\ModuleTransaction\Contracts\TransactionItem as ContractsTransacitonItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\PaymentDetail;
use Hanafalah\ModuleTransaction\Resources\TransactionItem\{
    ViewTransactionItem,
    ShowTransactionItem
};

class TransactionItem extends PackageManagement implements ContractsTransacitonItem
{
    protected array $__guard   = ['id', 'uuid', 'parent_id', 'reference_type', 'reference_id'];
    protected array $__add     = ['status'];
    protected string $__entity = 'TransactionItem';
    public static $transaction_item_model;

    protected array $__resources = [
        'view' => ViewTransactionItem::class,
        'show' => ShowTransactionItem::class
    ];

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }

    public function prepareShowTransactionItem(?Model $model = null): ?Model
    {
        $this->booting();

        $model ??= $this->getTransactionItem();
        $id = request()->id;
        if (!request()->has('id')) throw new \Exception('No id provided', 422);

        if (!isset($model)) $model = $this->trxItem()->find($id);
        return static::$transaction_item_model = $model;
    }

    public function showTransactionItem(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowTransactionItem($model);
        });
    }

    public function prepareStoreTransactionItem(mixed $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (isset($attributes['id'])) {
            $guard = ['id' => $attributes['id']];
        } else {
            $guard = [
                'transaction_id' => $attributes['transaction_id'],
                'parent_id'      => $attributes['parent_id'] ?? null,
                'item_type'      => $attributes['item_type'],
                'item_id'        => $attributes['item_id']
            ];
        }
        $transaction_item = $this->TransactionItemModel()->updateOrCreate($guard, [
            'item_name'      => $attributes['item_name']
        ]);
        if (isset($attributes['payment_detail'])) {
            //CREATE PAYMENT DETAIL
            $transaction     = $this->TransactionModel()->find($attributes['transaction_id']);
            if (!isset($attributes['payment_detail']['payment_summary_id'])) {
                $payment_summary = $transaction->paymentSummary()->firstOrCreate();
            } else {
                $payment_summary = $this->PaymentSummaryModel()->findOrFail($attributes['payment_detail']['payment_summary_id']);
            }

            $payment_detail = $this->mergeArray($attributes['payment_detail'], [
                'payment_summary_id'  => $payment_summary->getKey(),
                'transaction_item_id' => $transaction_item->getKey()
            ]);
            $payment_detail_schema = $this->schemaContract('payment_detail');
            $payment_detail_schema->prepareStorePaymentDetail($payment_detail);
        }
        return static::$transaction_item_model = $transaction_item;
    }

    public function storeTransactionItem(): array
    {
        return $this->transaction(function () {
            return $this->showTransactionItem($this->prepareStoreTransactionItem());
        });
    }

    protected function trxItem(mixed $conditionals = null): Builder
    {
        return $this->TransactionItemModel()->conditionals($conditionals);
    }
}
