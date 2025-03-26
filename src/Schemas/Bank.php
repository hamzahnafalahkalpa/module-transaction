<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Bank as ContractsBank;
use Hanafalah\ModuleTransaction\Data\BankData;
use Hanafalah\ModuleTransaction\Resources\Bank\{ViewBank, ShowBank};

class Bank extends PackageManagement implements ContractsBank
{
    protected string $__entity = 'bank';
    public static $bank_model;

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [];
    }

    public function getBank(): mixed{
        return static::$bank_model;
    }

    public function prepareStoreBank(BankData $bank_dto): Model{
        if (isset($bank_dto->id)) {
            $guard = ['id' => $bank_dto->id];
        }else{
            $guard = [
                'name'           => $bank_dto->name,
                'account_number' => $bank_dto->account_number,
                'account_name'   => $bank_dto->account_name
            ];
        }

        $model = $this->BankModel()->updateOrCreate($guard,[
            'status' => $bank_dto->status
        ]);

        return static::$bank_model = $model;
    }

    public function storeBank(?BankData $bank_dto = null): array{
        return $this->transaction(function() use ($bank_dto){
            return $this->showBank($this->prepareStoreBank($bank_dto ?? $this->requestDTO(BankData::class)));
        });
    }

    public function prepareShowBank(?Model $model = null, ?array $attributes = null): Model{
        $attributes ??= request()->all();

        $model ??= $this->getBank();
        if (isset($attributes['id'])) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Id not found');
            $model = $this->bank()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return static::$bank_model = $model;
    }

    public function showBank(?Model $model = null): array{
        return $this->showEntityResource(function() use ($model){
            return $this->prepareShowBank($model);
        });
    }

    public function prepareViewBankList(?array $attributes = null): Collection{
        $attributes ??= request()->all();

        return static::$bank_model = $this->bank()->with($this->viewUsingRelation())->orderBy('name', 'asc')->get();
    }

    public function viewBankList(): array{
        return $this->viewEntityResource(function(){
            return $this->prepareViewBanklist();
        });
    }

    public function prepareDeleteBank(?array $attributes = null): bool{
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Id not found');

        $model = $this->bank()->findOrFail($attributes['id']);
        return $model->delete();
    }

    public function deleteBank(): bool{
        return $this->transaction(function () {
            return $this->prepareDeleteBank();
        });
    }

    public function bank(mixed $conditionals = null): Builder{
        $this->booting();
        return $this->BankModel()->withParameters()->conditionals($this->mergeCondition($conditionals ?? []))->orderBy('name','asc');
    }
}
