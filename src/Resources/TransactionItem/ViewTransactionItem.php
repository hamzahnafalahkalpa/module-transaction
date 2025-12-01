<?php

namespace Hanafalah\ModuleTransaction\Resources\TransactionItem;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewTransactionItem extends ApiResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'              => $this->id,
            'name'            => $this->name,
            'transaction_id'  => $this->transaction_id,
            'item_type'       => $this->item_type,
            'item_id'         => $this->item_id,
            'payment_detail'  => $this->relationValidation('paymentDetail',function(){
                return $this->paymentDetail->toViewApi()->resolve();
            }),
            'item'            => $this->relationValidation('item', function () {
                return $this->item->toShowApi()->resolve();
            },$this->prop_item),
            'dynamic_forms' => $this->dynamic_forms,
        ];
        return $arr;
    }
}
