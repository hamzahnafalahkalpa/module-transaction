<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface Billing extends DataManagement
{
    public function addOrChange(?array $attributes = []): self;
    public function initializeBilling(Model $billing): void;
    public function viewUsingRelation(): array;
    public function prepareViewBillingPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewBillingPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array;
    public function prepareStoreBilling(?array $attributes = null): Model;
    public function prepareShowBilling(?Model $model = null): ?Model;
    public function showBilling(?Model $model = null): array;
    public function billing(mixed $conditionals = null): Builder;
    public function get(mixed $conditionals = null): Collection;
    public function getBilling(): mixed;
}
