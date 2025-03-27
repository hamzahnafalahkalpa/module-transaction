<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Data\PaginateData;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Deposit as ContractsDeposit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class Deposit extends PackageManagement implements ContractsDeposit
{
    protected array $__guard   = ['id', 'reference_type', 'reference_id'];
    protected array $__add     = ['total', 'reported_at'];
    protected string $__entity = 'Deposit';
    public static $deposit_model;

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [];
    }

    public function getDeposit(): mixed{
        return static::$deposit_model;
    }

    public function prepareShowDeposit(? Model $model = null, ?array $attributes = null): Model{
        $attributes ??= request()->all();

        $model ??= $this->getDeposit();
        if (!isset($model)){
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Id not found');
            $model = $this->deposit()->with($this->showUsingRelation())->findOrFail($id);
        }else{
            $model->load($this->showUsingRelation());
        }

        return static::$deposit_model = $model;
    }

    public function prepareViewDepositPaginate(PaginateData $paginate_dto): LengthAwarePaginator{
        $model = $this->deposit()->paginate(...$paginate_dto->toArray())->appends(request()->all());
        return static::$deposit_model = $model;
    }

    public function viewDepositPaginate(?PaginateData $paginate_dto = null): array{
        return $this->viewEntityResource(function() use ($paginate_dto){
            return $this->prepareViewDepositPaginate($paginate_dto ?? $this->requestDTO(PaginateData::class));
        });
    }

    protected function deposit(mixed $conditionals = null): Builder{
        return $this->DepositModel()->conditionals($this->mergeCondition($conditionals))->orderBy('created_at','desc');
    }
}
