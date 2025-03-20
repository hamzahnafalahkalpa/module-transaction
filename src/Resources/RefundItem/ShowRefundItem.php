<?php

namespace Zahzah\ModuleTransaction\Resources\RefundItem;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ShowRefundItem extends ViewRefundItem
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
        ];

        $arr = array_merge(parent::toArray($request),$arr);
        
        return $arr;
    }
}