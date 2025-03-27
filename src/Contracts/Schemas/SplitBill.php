<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface SplitBill extends DataManagement
{
    public function prepareStoreSplitBill(?array $attributes = null): Model;
    public function prepareShowSplitBill(?Model $model = null): ?Model;
    public function showSplitBill(?Model $model = null): array;
    public function splitBill(mixed $conditionals = null): Builder;
    public function getSplitBill(): mixed;
}
