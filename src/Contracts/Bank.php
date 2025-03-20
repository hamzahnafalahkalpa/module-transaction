<?php

namespace Zahzah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Zahzah\LaravelSupport\Contracts\DataManagement;

interface Bank extends DataManagement
{
    public function showUsingRelation(): array;
    public function getBank(): mixed;
    public function prepareStoreBank(? array $attributes = null): Model;
    public function storeBank(): array;
    public function prepareShowBank(? Model $model = null, ? array $attributes = null): Model;
    public function showBank(? Model $model = null): array;
    public function prepareViewBankList(? array $attributes = null): Collection;
    public function viewBankList(): array;
    public function prepareDeleteBank(? array $attributes = null): bool;
    public function deleteBank(): bool;
    public function bank(): Builder;
     
}