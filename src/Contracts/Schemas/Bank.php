<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModuleTransaction\Data\BankData;

interface Bank extends DataManagement
{
    public function viewUsingRelation(): array;
    public function showUsingRelation(): array;
    public function getBank(): mixed;
    public function prepareStoreBank(BankData $bank_dto): Model;
    public function storeBank(?BankData $bank_dto = null): array;
    public function prepareShowBank(?Model $model = null, ?array $attributes = null): Model;
    public function showBank(?Model $model = null): array;
    public function prepareViewBankList(?array $attributes = null): Collection;
    public function viewBankList(): array;
    public function prepareDeleteBank(?array $attributes = null): bool;
    public function deleteBank(): bool;
    public function bank(mixed $conditionals = null): Builder;
    
}
