<?php

namespace Hanafalah\ModuleTransaction\Resources\RefundItem;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewRefundItem extends ApiResource
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
            'id'     => $this->id,
            'refund' => $this->relationValidation('refund', function () {
                return $this->refund->toViewApi();
            }),
            'item'   => $this->relationValidation('item', function () {
                return $this->item->toViewApi();
            })
        ];

        return $arr;
    }
}
