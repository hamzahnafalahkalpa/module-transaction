<?php

namespace Zahzah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Zahzah\LaravelSupport\Contracts\DataManagement;

interface Voucher extends DataManagement
{
    public function showUsingRelation(): array;
    public function getVoucher(): mixed;
    public function prepareStoreVoucher(? array $attributes = null): Model;
    public function storeVoucher(): array ;
    public function prepareShowVoucher(? Model $model = null, ? array $attributes = null): Model;
    public function showVoucher(? Model $model = null): array;
    public function prepareRevalidateVoucher(mixed $vouchers,array &$attributes): mixed;
    public function prepareViewVoucherList(? array $attributes = null): Collection;
    public function viewVoucherList(): array ;
    public function prepareDeleteVoucher(? array $attributes = null): Model;
    public function deleteVoucher(): Model;
    public function voucher(): Builder;
    
}
