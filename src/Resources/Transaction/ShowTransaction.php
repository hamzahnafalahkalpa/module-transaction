<?php

namespace Hanafalah\ModuleTransaction\Resources\Transaction;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModuleTransaction\Resources\PaymentDetail\ShowPaymentDetail;
use Hanafalah\ModuleTransaction\Resources\PaymentSummary\ShowPaymentSummary;

class ShowTransaction extends ViewTransaction
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
            'reference' => $this->relationValidation('reference', function () {
                return  $this->reference->toShowApi()->resolve();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
