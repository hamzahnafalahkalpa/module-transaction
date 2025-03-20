<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Transaction as ContractsTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModuleTransaction\Enums\Transaction\TransactionStatus;
use Hanafalah\ModuleTransaction\Resources\Transaction\{
    ShowTransaction,
    ViewTransaction
};

class Transaction extends PackageManagement implements ContractsTransaction
{
    protected array $__guard   = [
        'id',
        'uuid',
        'parent_id',
        'reference_type',
        'reference_id'
    ];
    protected array $__add     = ['status', 'invoice_id'];
    protected string $__entity = 'Transaction';
    public static $transaction_model;

    protected array $__resources = [
        'view' => ViewTransaction::class,
        'show' => ShowTransaction::class
    ];

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

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }

    public function initializeTransaction(Model $transaction): void
    {
        static::$transaction_model = $transaction;
    }

    public function prepareStoreTransactionItem(array $attributes): Model
    {
        return static::$transaction_model->transactionItem()->updateOrCreate([
            'id'             => $attributes['id'] ?? null,
            'transaction_id' => static::$transaction_model->getKey(),
            'item_type'      => $attributes['item_type'],
            'item_id'        => $attributes['item_id']
        ]);
    }

    public function getTransactionStatus(): array
    {
        $status = TransactionStatus::cases();
        return array_reduce(array_map(function ($status) {
            return [$status->name => $status->value];
        }, $status), 'array_merge', []);
    }


    private function localAddSuffixCache(mixed $suffix): void
    {
        $this->addSuffixCache($this->__cache['index'], "service-index", $suffix);
    }

    protected function getTransactionBuilder($morphs)
    {
        $status = $this->getTransactionStatus();
        $morphs = $this->mustArray($morphs);
        return $this->trx(function ($query) use ($morphs) {
            $query->when(isset($morphs), function ($query) use ($morphs) {
                $query->whereIn('reference_type', $morphs);
            });
        })->with($this->showUsingRelation())
            ->whereIn('status', [$status['DRAFT'], $status['ACTIVE']]);
    }

    public function prepareViewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator
    {
        $morphs ??= $cache_reference_type;
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        $cache_reference_type ??= 'all';
        $cache_reference_type .= '-paginate';
        $this->localAddSuffixCache($cache_reference_type);
        // return $this->cacheWhen(!$this->isSearch(),$this->__cache['index'],function() use ($morphs,$paginate_options){
        return $this->getTransactionBuilder($morphs)
            ->paginate(...$this->arrayValues($paginate_options))
            ->appends(request()->all());
        // });
    }

    public function viewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, int $perPage = 10, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['view'], function () use ($cache_reference_type, $morphs, $paginate_options) {
            return $this->prepareViewTransactionPaginate($cache_reference_type, $morphs, ...$this->arrayValues($paginate_options));
        });
    }

    protected function showUsingRelation(): array
    {
        return [
            "reference.patient",
            "transactionItems",
            "paymentSummary"
        ];
    }

    public function prepareShowTransaction(?Model $model = null): ?Model
    {
        $this->booting();

        $model ??= $this->getTransaction();
        if (!isset($model)) {
            $id = request()->id;
            if (!request()->has('id')) throw new \Exception('No id provided', 422);
            $model = $this->trx()->with($this->showUsingRelation())->find($id);
        } else {
            $model->load($this->showUsingRelation());
        }

        return static::$transaction_model = $model;
    }

    public function showTransaction(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], $this->prepareShowTransaction($model));
    }

    public function getTransaction(): mixed
    {
        return static::$transaction_model;
    }

    protected function trx(mixed $conditionals = null): Builder
    {
        return $this->TransactionModel()->withParameters($this->getParamLogic())
            ->conditionals($conditionals ?? []);
    }

    public function getTransactionList($conditionls = null): Collection
    {
        return $this->MasterFeatureModel()->conditionals($conditionls)->get();
    }
    //END GETTER SECTION


}
