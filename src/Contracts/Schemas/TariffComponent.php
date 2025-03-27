<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface TariffComponent extends DataManagement
{
    public function addOrChange(?array $attributes = []): self;
    public function prepareStoreTariffComponent(?array $attributes = null): Model;
    public function storeTariffComponent(): array;
    public function prepareShowTariffComponent(?Model $model = null): ?Model;
    public function showTariffComponent(?Model $model = null): array;
    public function prepareRemoveTariffComponent(): bool;
    public function removeTariffComponentById(): bool;
    public function prepareViewTariffComponentList(string|array $flags): Collection;
    public function prepareViewTariffComponentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewTariffComponentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array;
    public function viewTariffComponentList(string|array $flags): array;
    public function getTariffComponent(): mixed;
}
