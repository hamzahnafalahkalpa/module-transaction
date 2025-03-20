<?php

namespace Hanafalah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\DataManagement;

interface Consument extends DataManagement
{
    public function prepareStoreConsument(?array $attributes = null): Model;
    public function showUsingRelation(): array;
    public function prepareShowConsument(?Model $model = null, ?array $attributes = null): Model;
    public function showConsument(?Model $model = null): array;
    public function prepareViewConsumentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewConsumentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array;
    public function prepareDeleteConsument(?array $attributes = null): bool;
    public function deleteConsument(): bool;
    public function consument(mixed $conditionals = null): Builder;
    public function getConsument(): mixed;
}
