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
            'transaction_id'  => $this->transaction_id,
            'item_type'       => $this->item_type,
            'item_id'         => $this->item_id,
            'item_name'       => $this->item_name
        ];

        return $arr;
    }
}
