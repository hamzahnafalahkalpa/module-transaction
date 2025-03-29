<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Contracts\Data\PaginateData;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Transaction as ContractsTransaction;
use Hanafalah\ModuleTransaction\Data\TransactionItemData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModuleTransaction\Enums\Transaction\Status;

class Transaction extends PackageManagement implements ContractsTransaction
{
    protected string $__entity = 'Transaction';
    public static $transaction_model;

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

    protected function viewUsingRelation(): array{
        return [];
    }

    protected function showUsingRelation(): array{
        return [
            'reference.patient',
            'transactionItems',
            'paymentSummary'
        ];
    }

    public function getTransaction(): mixed{
        return static::$transaction_model;
    }

    public function initializeTransaction(Model $transaction): void{
        static::$transaction_model = $transaction;
    }

    public function prepareStoreTransactionItem(TransactionItemData $transaction_item_dto): Model{
        if (isset($transaction_item_dto->id)){
            $guard = ['id' => $transaction_item_dto->id];
        }else{
            $guard = [
                'transaction_id' => $transaction_item_dto->transaction_id,
                'item_type'      => $transaction_item_dto->item_type,
                'item_id'        => $transaction_item_dto->item_id    
            ];
        }
        return static::$transaction_model->transactionItem()->updateOrCreate($guard);
    }

    public function storeTransactionItem(? TransactionItemData $transaction_item_dto = null): array{
        return $this->transaction(function() use ($transaction_item_dto){
            return $this->prepareStoreTransactionItem($transaction_item_dto ?? $this->requestDTO(TransactionItemData::class));
        });
    }

    public function getTransactionStatus(): array{
        $status = Status::cases();
        return array_reduce(array_map(function ($status) {
            return [$status->name => $status->value];
        }, $status), 'array_merge', []);
    }


    private function localAddSuffixCache(mixed $suffix): void{
        $this->addSuffixCache($this->__cache['index'], "service-index", $suffix);
    }

    public function prepareViewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, PaginateData $paginate_dto): LengthAwarePaginator{
        $morphs ??= $cache_reference_type;
        $cache_reference_type ??= 'all';
        $cache_reference_type .= '-paginate';
        $this->localAddSuffixCache($cache_reference_type);
        return $this->commonTransaction($morphs)->paginate(...$paginate_dto->toArray())->appends(request()->all());
    }

    public function viewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, ? PaginateData $paginate_dto): array{
        return $this->viewEntityResource(function() use ($cache_reference_type, $morphs, $paginate_dto){
            return $this->prepareViewTransactionPaginate($cache_reference_type, $morphs, $paginate_dto ?? $this->requestDTO(PaginateData::class));
        });
    }    

    public function prepareShowTransaction(?Model $model = null, ? array $attributes = null): ?Model{
        $model ??= $this->getTransaction();
        if (!isset($model)) {
            $id = request()->id;
            if (!request()->has('id')) throw new \Exception('No id provided', 422);
            $model = $this->trx()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }

        return static::$transaction_model = $model;
    }

    public function showTransaction(?Model $model = null): array{
        return $this->showEntityResource(function() use ($model){
            return $this->prepareShowTransaction($model);
        });
    }

    protected function commonTransaction($morphs){
        $status = $this->getTransactionStatus();
        return $this->trx(function ($query) use ($morphs) {
            $query->when(isset($morphs), function ($query) use ($morphs) {
                $query->whereIn('reference_type', $this->mustArray($morphs));
            });
        })->with($this->showUsingRelation())
        ->whereIn('status', [$status['DRAFT'], $status['ACTIVE']]);
    }

    protected function trx(mixed $conditionals = null): Builder{
        $this->booting();
        return $this->TransactionModel()->withParameters($this->getParamLogic())->conditionals($this->mergeCondition($conditionals ?? []))
                    ->orderBy('created_at','desc');
    }
}
