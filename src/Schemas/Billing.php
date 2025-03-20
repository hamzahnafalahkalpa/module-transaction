<?php

namespace Zahzah\ModuleTransaction\Schemas;

use Zahzah\LaravelSupport\Supports\PackageManagement;
use Zahzah\ModuleTransaction\Contracts\Billing as ContractsBilling;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Zahzah\ModuleTransaction\Resources\Billing\{
    ShowBilling, ViewBilling
};

class Billing extends PackageManagement implements ContractsBilling{
    protected array $__guard   = ['id', 'transaction_id'];
    protected array $__add     = ['cashier_type','cashier_id','author_type','author_id','status','invoice_id'];
    protected string $__entity = 'Billing';
    public static $billing_model;

    protected array $__resources = [
        'view' => ViewBilling::class,
        'show' => ShowBilling::class
    ];

    public function addOrChange(? array $attributes=[]): self{
        $this->updateOrCreate($attributes);
        return $this;
    }

    public function initializeBilling(Model $billing): void{
        static::$billing_model = $billing;
    }

    protected function showUsingRelation(): array {
        return [
            'author','transaction','cashier',
            'splitBills.paymentHistory.paymentHistoryDetails'
        ];
    }

    public function viewUsingRelation(): array{
        return ['hasTransaction.reference'];
    }

    public function prepareViewBillingPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): LengthAwarePaginator{
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        $billing = $this->billing()->with($this->viewUsingRelation())->paginate(
            ...$this->arrayValues($paginate_options)
        )->appends(request()->all());

        return static::$billing_model = $billing;
    }

    public function viewBillingPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): array{
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');

        return $this->transforming($this->__resources['view'],function() use ($paginate_options){
            return $this->prepareViewBillingPaginate(...$this->arrayValues($paginate_options));
        });
    }

    public function prepareStoreBilling(? array $attributes = null): Model{
        $attributes ??= request()->all();

        if (isset($attributes['id'])){
            $guard = ['id' => $attributes['id']];
        }else{
            $guard = [
                'reported_at'    => null,
                'transaction_id' => $attributes['transaction_id'],
            ];
        }

        $billing = $this->BillingModel()->updateOrCreate($guard,[
            'author_type'  => $attributes['author_type'] ?? null,
            'author_id'    => $attributes['author_id'] ?? null,
            'cashier_type' => $attributes['cashier_type'] ?? null,
            'cashier_id'   => $attributes['cashier_id'] ?? null,
            'reported_at'  => $attributes['reported_at'] ?? null
        ]);

        $this->createBillingTransaction($billing);

        $split_bill_schema = $this->schemaContract('split_bill');
        if(!isset($attributes['split_bills'])) throw new \Exception("split bill not found");
        foreach ($attributes['split_bills'] as $split_bill_attr){
            if (!isset($split_bill_attr['payment_summaries']) || count($split_bill_attr['payment_summaries']) == 0) {
                dd("ini dd di blling schema karena payment_summaries nya gaada datanya");
            }

            $split_bill_schema->prepareStoreSplitBill(array_merge($split_bill_attr,[
                'billing'    => $billing,
                'billing_id' => $billing->getKey()
            ]));
        }
        $billing->reported_at = now();
        $billing->save();
        $billing->reporting();
        $billing->refresh();
        $billing->load(['splitBills.paymentHistory.paymentHistoryDetails']);

        return static::$billing_model = $billing;
    }

    protected function createBillingTransaction(Model &$billing): void{
        $biling_transaction            = $billing->transaction()->firstOrCreate();
        $biling_transaction->parent_id = $billing->transaction_id;
        $biling_transaction->save();

        $billing->setRelation('transaction',$biling_transaction);
    }

    public function prepareShowBilling(? Model $model = null): ?Model{
        $this->booting();
        $model ??= $this->getBilling();
        if (!isset($model)){
            $model = isset(request()->id) ? $this->findBillingById() : $this->findBillingByUuid();
        }else{
            $model->load($this->showUsingRelation());
        }

        return static::$billing_model = $model;
    }

    protected function findBillingById(): Model{
        return $this->billing()->with($this->showUsingRelation())->find(request()->id);
    }

    protected function findBillingByUuid(): Model{
        return $this->billing()->with($this->showUsingRelation())->when(
                isset(request()->uuid),
                fn($q) => $q->where("uuid",request()->uuid)
        )->first();
    }

    public function showBilling(? Model $model = null): array{
        return $this->transforming($this->__resources['show'],$this->prepareShowBilling($model));
    }

    public function billing(mixed $conditionals = null): Builder{
        $this->booting();
        return $this->BillingModel()->withParameters()->conditionals($conditionals)->orderBy('created_at','desc');
    }

    public function get(mixed $conditionals = null): Collection{
        return $this->billing($conditionals)->get();
    }

    public function getBilling(): mixed{
        return static::$billing_model;
    }
}
