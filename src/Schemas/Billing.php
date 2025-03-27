<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Data\PaginateData;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Billing as ContractsBilling;
use Hanafalah\ModuleTransaction\Data\BillingData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModuleTransaction\Resources\Billing\{
    ShowBilling,
    ViewBilling
};

class Billing extends PackageManagement implements ContractsBilling
{
    protected string $__entity = 'Billing';
    public static $billing_model;

    protected function viewUsingRelation(): array{
        return ['hasTransaction.reference'];
    }

    protected function showUsingRelation(): array{
        return [
            'author',
            'transaction',
            'cashier',
            'splitBills.paymentHistory.paymentHistoryDetails'
        ];
    }

    public function getBilling(): mixed{
        return static::$billing_model;
    }

    public function initializeBilling(Model $billing): void{
        static::$billing_model = $billing;
    }

    public function prepareViewBillingPaginate(PaginateData $paginate_dto): LengthAwarePaginator{
        $billing = $this->billing()->with($this->viewUsingRelation())->paginate(
            ...$paginate_dto->toArray()
        )->appends(request()->all());

        return static::$billing_model = $billing;
    }

    public function viewBillingPaginate(?PaginateData $paginate_dto = null): array{
        return $this->viewEntityResource(function() use ($paginate_dto){
            return $this->prepareViewBillingPaginate($paginate_dto ?? PaginateData::from(request()->all()));
        });
    }

    public function prepareStoreBilling(BillingData $billing_dto): Model{
        if (isset($billing_dto->id)) {
            $guard = ['id' => $billing_dto->id];
        } else {
            $guard = [
                'reported_at'    => null,
                'transaction_id' => $billing_dto->transaction_id
            ];
        }

        $billing = $this->BillingModel()->updateOrCreate($guard, [
            'author_type'  => $billing_dto->author_type,
            'author_id'    => $billing_dto->author_id,
            'cashier_type' => $billing_dto->cashier_type,
            'cashier_id'   => $billing_dto->cashier_id,
            'reported_at'  => $billing_dto->reported_at
        ]);

        $this->createBillingTransaction($billing);

        $split_bill_schema = $this->schemaContract('split_bill');
        if (!isset($billing_dto->split_bills)) throw new \Exception("split bill not found");
        foreach ($billing_dto->split_bills as $split_bill_attr) {
            $split_bill_schema->prepareStoreSplitBill(array_merge($split_bill_attr, [
                'billing'    => $billing,
                'billing_id' => $billing->getKey()
            ]));
        }
        $billing->reported_at = now();
        $billing->save();

        $billing->refresh();
        $billing->load(['splitBills.paymentHistory.paymentHistoryDetails']);

        return static::$billing_model = $billing;
    }

    public function storeBilling(?BillingData $billing_dto = null): array{
        return $this->transaction(function() use ($billing_dto){
            return $this->prepareStoreBilling($billing_dto ?? BillingData::from());
        });
    }

    protected function createBillingTransaction(Model &$billing): void{
        $biling_transaction            = $billing->transaction()->firstOrCreate();
        $biling_transaction->parent_id = $billing->transaction_id;
        $biling_transaction->save();

        $billing->setRelation('transaction', $biling_transaction);
    }

    public function prepareShowBilling(?Model $model = null): ?Model{
        $this->booting();
        $model ??= $this->getBilling();
        if (!isset($model)) {
            $model = isset(request()->id) ? $this->findBillingById() : $this->findBillingByUuid();
        } else {
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
            fn($q) => $q->uuid(request()->uuid)
        )->first();
    }

    public function showBilling(?Model $model = null): array{
        return $this->showEntityResource(function() use ($model){
            return $this->prepareShowBilling($model);
        });
    }

    public function billing(mixed $conditionals = null): Builder{
        $this->booting();
        return $this->BillingModel()->withParameters()->conditionals($this->mergeCondition($conditionals ?? []))->orderBy('created_at', 'desc');
    }    
}
