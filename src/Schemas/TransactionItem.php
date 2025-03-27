<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\ModuleTransaction\Contracts\Schemas\TransactionItem as ContractsTransacitonItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Data\TransactionItemData;

class TransactionItem extends PackageManagement implements ContractsTransacitonItem
{
    protected string $__entity = 'TransactionItem';
    public static $transaction_item_model;

    protected function viewUsingRelation(): array{
        return [];
    }

    protected function showUsingRelation(): array{
        return [];
    }

    public function getTransactionItem(): mixed{
        return static::$transaction_item_model;
    }

    public function prepareShowTransactionItem(?Model $model = null, ? array $attributes = null): ?Model{
        $attributes ??= request()->all();

        $model ??= $this->getTransactionItem();
        if (!isset($model)){
            $id = $attributes['id'] ?? null;
            if (!$id) throw new \Exception('No id provided', 422);
            $model = $this->trxItem()->with($this->showUsingRelation())->findOrFail($id);
        }else{
            $model->load($this->showUsingRelation());
        }
        return static::$transaction_item_model = $model;
    }

    public function showTransactionItem(?Model $model = null): array{
        return $this->showEntityResource(function() use ($model){
            return $this->prepareShowTransactionItem($model);
        });
    }

    public function prepareStoreTransactionItem(TransactionItemData $transaction_item_dto): Model{
        if (isset($transaction_item_dto->id)) {
            $guard = ['id' => $transaction_item_dto->id];
        } else {
            $guard = [
                'transaction_id' => $transaction_item_dto->transaction_id,
                'parent_id'      => $transaction_item_dto->parent_id ?? null,
                'item_type'      => $transaction_item_dto->item_type,
                'item_id'        => $transaction_item_dto->item_id
            ];
        }

        $transaction_item = $this->TransactionItemModel()->updateOrCreate($guard, [
            'item_name' => $transaction_item_dto->item_name
        ]);
        if (isset($transaction_item_dto->payment_detail)) {
            if ($this->PaymentDetailModel() == null) throw new \Exception('You need to install module-payment', 422);

            $this->schemaContract('payment_detail')
                 ->prepareStorePaymentDetail($transaction_item_dto->payment_detail);
        }
        return static::$transaction_item_model = $transaction_item;
    }

    public function storeTransactionItem(?TransactionItemData $transaction_item_dto = null): array{
        return $this->transaction(function() use ($transaction_item_dto){
            return $this->showTransactionItem($this->prepareStoreTransactionItem($transaction_item_dto ?? $this->requestDTO(TransactionItemData::class)));
        });
    }

    protected function trxItem(mixed $conditionals = null): Builder{
        return $this->TransactionItemModel()->withParameters()
                    ->conditionals($this->mergeCondition($conditionals ?? []))
                    ->orderBy('created_at','asc');
    }
}
