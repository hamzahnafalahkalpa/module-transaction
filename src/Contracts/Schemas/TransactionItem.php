<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModuleTransaction\Data\TransactionItemData;
use Illuminate\Database\Eloquent\Builder;

/**
 * @see \Hanafalah\ModuleTransaction\Schemas\TransactionItem
 * @method self conditionals(mixed $conditionals)
 * @method array updateTransactionItem(?TransactionItemData $transaction_item_dto = null)
 * @method Model prepareUpdateTransactionItem(TransactionItemData $transaction_item_dto)
 * @method bool deleteTransactionItem()
 * @method bool prepareDeleteTransactionItem(? array $attributes = null)
 * @method mixed getTransactionItem()
 * @method ?Model prepareShowTransactionItem(?Model $model = null, ?array $attributes = null)
 * @method array showTransactionItem(?Model $model = null)
 * @method Collection prepareViewTransactionItemList()
 * @method array viewTransactionItemList()
 * @method LengthAwarePaginator prepareViewTransactionItemPaginate(PaginateData $paginate_dto)
 * @method array viewTransactionItemPaginate(?PaginateData $paginate_dto = null)
 * @method array storeTransactionItem(?TransactionItemData $transaction_item_dto = null);
 */
interface TransactionItem extends DataManagement
{
    public function prepareStoreTransactionItem(TransactionItemData $transaction_item_dto): Model;
    public function camelEntity(): string;
    public function trxItem(mixed $conditionals = null): Builder;
}
