<?php

namespace Hanafalah\ModuleTransaction\Resources\Refund;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ShowRefund extends ViewRefund
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
            'refund_items' => $this->relationValidation('refundItems', function () {
                return $this->refundItems->transform(function ($refundItem) {
                    return $refundItem->toViewApi();
                });
            })
        ];

        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
