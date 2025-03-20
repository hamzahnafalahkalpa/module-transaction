<?php

namespace Zahzah\ModuleTransaction\Resources\TransactionItem;

use Zahzah\LaravelSupport\Resources\ApiResource;

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
            'item'            => $this->relationValidation('item',function(){
                $item = $this->item;
                return $item->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request),$arr);
        
        return $arr;
    }
}