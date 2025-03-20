<?php

namespace Hanafalah\ModuleTransaction\Resources\Billing;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ShowBilling extends ViewBilling
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
            'author'         => $this->relationValidation('author', function () {
                return $this->author->toShowApi();
            }),
            'cashier'        => $this->relationValidation('cashier', function () {
                return $this->cashier->toShowApi();
            }),
            'split_bills' => $this->relationValidation('splitBills', function () {
                return $this->splitBills->map(function ($splitBill) {
                    return $splitBill->toShowApi();
                });
            }),
            'splitBills' => $this->relationValidation('splitBills', function () {
                return $this->splitBills->map(function ($splitBill) {
                    return $splitBill->toShowApi();
                });
            }),
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
