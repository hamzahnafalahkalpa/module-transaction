<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Transaction as ContractsTransaction;
use Illuminate\Database\Eloquent\Builder;

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

    public function camelEntity(): string{
        return 'trx';
    }

    public function trx(mixed $conditionals = null): Builder{
        return $this->generalSchemaModel($conditionals)->when(isset(request()->reference_type),function($query){
            return $query->where('reference_type',request()->reference_type);
        });
    }
}
