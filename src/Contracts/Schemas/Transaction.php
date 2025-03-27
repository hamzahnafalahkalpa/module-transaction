<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\LaravelSupport\Data\PaginateData;
use Hanafalah\ModuleTransaction\Data\TransactionItemData;

interface Transaction extends DataManagement
{
    public function getTransaction(): mixed;
    public function initializeTransaction(Model $transaction): void;
    public function prepareStoreTransactionItem(TransactionItemData $transaction_item_dto): Model;
    public function storeTransactionItem(? TransactionItemData $transaction_item_dto = null): array;
    public function getTransactionStatus(): array;
    public function prepareViewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, PaginateData $paginate_dto): LengthAwarePaginator;
    public function viewTransactionPaginate(mixed $cache_reference_type = null, ?array $morphs = null, ? PaginateData $paginate_dto): array;
    public function prepareShowTransaction(?Model $model = null, ? array $attributes = null): ?Model;
    public function showTransaction(?Model $model = null): array;
    
}
