<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\VoucherRule as ContractsVoucherRule;
use Hanafalah\ModuleTransaction\Resources\VoucherRule\{ViewVoucherRule, ShowVoucherRule};

class VoucherRule extends PackageManagement implements ContractsVoucherRule
{
    public static $voucher_rule_model;


    protected array $__resources = [
        'view' => ViewVoucherRule::class,
        'show' => ShowVoucherRule::class
    ];

    public function showUsingRelation(): array
    {
        return ['voucher'];
    }

    public function getVoucherRule(): mixed
    {
        return static::$voucher_rule_model;
    }

    public function prepareStoreVoucherRule(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $model = $this->VoucherRuleModel()->updateOrCreate([
            'id' => $attributes['id'] ?? null
        ], [
            'name'       => $attributes['name'],
            'voucher_id' => $attributes['voucher_id'],
            'condition'  => $attributes['condition']
        ]);
        $model->rule = $attributes['rule'] ?? null;
        $model->save();

        return static::$voucher_rule_model = $model;
    }

    public function storeVoucherRule(): array
    {
        return $this->transaction(function () {
            return $this->prrepareStoreVoucherRule();
        });
    }

    public function prepareShowVoucherRule(?Model $model = null, ?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $model ??= $this->getVoucherRule();
        if (isset($attributes['id'])) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Id not found');
            $model = $model->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return static::$voucher_rule_model = $model;
    }

    public function showVoucherRule(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowVoucherRule($model);
        });
    }

    public function prepareViewVoucherRuleList(?array $attributes = null): array
    {
        $attributes ??= request()->all();

        return static::$voucher_rule_model = $this->voucherRule()->orderBy('name', 'asc')->get();
    }

    public function viewVoucherRuleList(): array
    {
        return $this->transforming($this->__resources['view'], function () {
            return $this->prepareViewVoucherRuleList();
        });
    }

    public function prepareDeleteVoucherRule(?array $attributes = null): bool
    {
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Id not found');

        $model = $this->voucherRule()->findOrFail($attributes['id']);
        return $model->delete();
    }

    public function deleteVoucherRule(): bool
    {
        return $this->transaction(function () {
            return $this->prepareDeleteVoucherRule();
        });
    }

    public function voucherRule(): Builder
    {
        $this->booting();
        return $this->VoucherRuleModel()->withParameters()->orderBy('name', 'asc');
    }
}
