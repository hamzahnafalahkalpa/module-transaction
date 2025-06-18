<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\LaravelSupport\Contracts\Data\PaginateData;
use Illuminate\Database\Eloquent\Builder;

/**
 * @see \Hanafalah\ModuleTransaction\Schemas\Transaction
 * @method self conditionals(mixed $conditionals)
 * @method mixed export(string $type)
 * @method array updateTransaction(?TransactionData $transaction_dto = null)
 * @method Model prepareUpdateTransaction(TransactionData $transaction_dto)
 * @method bool deleteTransaction()
 * @method bool prepareDeleteTransaction(? array $attributes = null)
 * @method mixed getTransaction()
 * @method ?Model prepareShowTransaction(?Model $model = null, ?array $attributes = null)
 * @method array showTransaction(?Model $model = null)
 * @method Collection prepareViewTransactionList()
 * @method array viewTransactionList()
 * @method LengthAwarePaginator prepareViewTransactionPaginate(PaginateData $paginate_dto)
 * @method array viewTransactionPaginate(?PaginateData $paginate_dto = null)
 * @method array storeTransaction(?TransactionData $transaction_dto = null);
 */
interface Transaction extends DataManagement
{
    public function trx(mixed $conditionals = null): Builder;
}
