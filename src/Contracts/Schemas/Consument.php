<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\LaravelSupport\Data\PaginateData;

interface Consument extends DataManagement
{
    public function viewUsingRelation(): array;
    public function showUsingRelation(): array;
    public function getConsument(): mixed;
    public function prepareStoreConsument(?array $attributes = null): Model;
    public function prepareShowConsument(?Model $model = null, ?array $attributes = null): Model;
    public function showConsument(?Model $model = null): array;
    public function prepareViewConsumentPaginate(PaginateData $paginate_dto): LengthAwarePaginator;
    public function viewConsumentPaginate(?PaginateData $paginate_dto = null): array;
    public function prepareDeleteConsument(?array $attributes = null): bool;
    public function deleteConsument(): bool;
    public function consument(mixed $conditionals = null): Builder;
    
}
