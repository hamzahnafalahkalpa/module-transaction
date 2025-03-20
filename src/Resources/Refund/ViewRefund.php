<?php

namespace Hanafalah\ModuleTransaction\Resources\Refund;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewRefund extends ApiResource
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
            'id'          => $this->id,
            'refund_code' => $this->refund_code,
            'author'      => $this->relationValidation('author', function () {
                return $this->author->toViewApi();
            }),
            'withdrawal_at' => $this->withdrawal_at,
            'total' => $this->total,
            'transaction' => $this->relationValidation('transaction', function () {
                return $this->transaction->toViewApi();
            }),
            'refund_items' => $this->relationValidation('refundItems', function () {
                return $this->refundItems->map(function ($refundItem) {
                    return $refundItem->toViewApi();
                });
            })
        ];
        $props = $this->getPropsData();
        foreach ($props as $key => $prop) {
            $arr[$key] = $prop;
        }

        return $arr;
    }
}
