<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Deposit as ContractsDeposit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Deposit extends PackageManagement implements ContractsDeposit
{

    public function booting(): self
    {
        static::$__class = $this;
        static::$__model = $this->{$this->__entity . "Model"}();
        return $this;
    }

    protected array $__guard   = ['id', 'reference_type', 'reference_id'];
    protected array $__add     = ['total', 'reported_at'];
    protected string $__entity = 'Deposit';

    /**
     * Add a new API access or update the existing one if found.
     *
     * The given attributes will be merged with the existing API access.
     *
     * @param array $attributes The attributes to be added to the API access.
     *
     * @return \Illuminate\Database\Eloquent\Model The API access model.
     */
    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }

    //GETTER SECTION
    protected function deposit(mixed $conditionals = null): Builder
    {
        return $this->DepositModel()->conditionals($conditionals);
    }

    public function get(mixed $conditionals = null): Collection
    {
        return $this->deposit($conditionals)->get();
    }
}
