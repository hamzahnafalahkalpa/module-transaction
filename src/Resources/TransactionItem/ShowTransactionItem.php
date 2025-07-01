<?php

namespace Hanafalah\ModuleTransaction\Resources\TransactionItem;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ShowTransactionItem extends ViewTransactionItem
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
            'item_name'       => $this->item_name,
            'item'            => $this->relationValidation('item', function () {
                return $this->item->toShowApi()->resolve();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
