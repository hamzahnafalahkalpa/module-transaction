<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface Transaction extends DataManagement
{
    public function addOrChange(?array $attributes = []): self;
    public function initializeTransaction(Model $transaction): void;
    public function prepareStoreTransactionItem(array $attributes): Model;
    public function getTransactionStatus(): array;
    public function prepareViewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, int $perPage = 10, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array;
    public function prepareShowTransaction(?Model $model = null): ?Model;
    public function showTransaction(?Model $model = null): array;
    public function getTransaction(): mixed;
    public function getTransactionList($conditionls = null): Collection;
}
