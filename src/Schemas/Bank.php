<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Bank as ContractsBank;
use Hanafalah\ModuleTransaction\Resources\Bank\{ViewBank, ShowBank};

class Bank extends PackageManagement implements ContractsBank
{
    protected string $__entity = 'bank';
    public static $bank_model;


    protected array $__resources = [
        'view' => ViewBank::class,
        'show' => ShowBank::class
    ];

    public function showUsingRelation(): array
    {
        return [];
    }

    public function getBank(): mixed
    {
        return static::$bank_model;
    }

    public function prepareStoreBank(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $model = $this->BankModel()->updateOrCreate([
            'id' => $attributes['id'] ?? null
        ], [
            'name'           => $attributes['name'],
            'account_number' => $attributes['account_number'],
            'account_name'   => $attributes['account_name'],
            'status'         => $attributes['status'] ?? null
        ]);

        return static::$bank_model = $model;
    }

    public function storeBank(): array
    {
        return $this->transaction(function () {
            return $this->showBank($this->prepareStoreBank());
        });
    }

    public function prepareShowBank(?Model $model = null, ?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $model ??= $this->getBank();
        if (isset($attributes['id'])) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Id not found');
            $model = $this->BankModel()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return static::$bank_model = $model;
    }

    public function showBank(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowBank($model);
        });
    }

    public function prepareViewBankList(?array $attributes = null): Collection
    {
        $attributes ??= request()->all();

        return static::$bank_model = $this->bank()->orderBy('name', 'asc')->get();
    }

    public function viewBankList(): array
    {
        return $this->transforming($this->__resources['view'], function () {
            return $this->prepareViewBanklist();
        });
    }

    public function prepareDeleteBank(?array $attributes = null): bool
    {
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Id not found');

        $model = $this->bank()->findOrFail($attributes['id']);
        return $model->delete();
    }

    public function deleteBank(): bool
    {
        return $this->transaction(function () {
            return $this->prepareDeleteBank();
        });
    }

    public function bank(): Builder
    {
        $this->booting();
        return $this->BankModel()->withParameters();
    }
}
