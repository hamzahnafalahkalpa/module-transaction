<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModuleTransaction\Data\TransactionItemData;

interface TransactionItem extends DataManagement
{
    public function getTransactionItem(): mixed;
    public function prepareShowTransactionItem(?Model $model = null, ? array $attributes = null): ?Model;
    public function showTransactionItem(?Model $model = null): array;
    public function prepareStoreTransactionItem(TransactionItemData $transaction_item_dto): Model;
    public function storeTransactionItem(?TransactionItemData $transaction_item_dto = null): array;
    
}
