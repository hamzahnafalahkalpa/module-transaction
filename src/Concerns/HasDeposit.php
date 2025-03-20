<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasDeposit
{
    public static function bootHasDeposit()
    {
        static::created(function ($query) {
            $query->deposit()->firstOrCreate();
        });
    }

    public function deposit()
    {
        return $this->morphOneModel('Deposit', 'reference');
    }
}
