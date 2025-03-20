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
        // if (isset(request()->childs) && request()->childs) {
        //     $this->load('childs');
        // }
        $arr = [
            'reference'       => $this->relationValidation('reference', function () {
                return  $this->reference->toShowApi();
            }),
            'visit_patient'   => $this->relationValidation('reference', function () {
                $this->reference->load(["services", "transaction"]);
                return $this->reference->toShowApi();
            }),
            'payment_summary' => $this->relationValidation('paymentSummary', function () {
                // $this->paymentSummary->load(['paymentDetails' => function($q) {
                //     $q->whereNotNull("payment_history_id")->with("paymentHistory");
                // }]);
                //                return $this->paymentSummary->toShowApi();
                return new ShowPaymentSummary($this->paymentSummary);
            }),
            'payment_history' => $this->relationValidation('paymentHistory', function () {
                return new ShowPaymentSummary($this->paymentHistory);
            }),
            'billing'         => $this->relationValidation('billing', function () {
                return $this->billing->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
