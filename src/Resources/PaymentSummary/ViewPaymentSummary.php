<?php

namespace Hanafalah\ModuleTransaction\Resources\PaymentSummary;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModuleTransaction\Models\Transaction\SplitBill;
use Hanafalah\ModuleTransaction\Resources\PaymentDetail\ViewPaymentDetail;

class ViewPaymentSummary extends ApiResource
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
            'id'               => $this->id,
            'reference_type'   => $this->reference_type,
            'total_amount'     => $this->total_amount,
            'total_discount'   => $this->total_discount,
            'total_debt'       => $this->total_debt,
            'total_additional' => $this->total_additional,
            'total_paid'       => $this->total_paid,
            'total_tax'        => $this->total_tax,
            'note'             => $this->note,
            'reference'        => $this->relationValidation('reference', function () {
                return $this->reference->toViewApi();
            }),
            'payment_details'  => $this->relationValidation('paymentDetails', function () {
                $paymentDetails = $this->paymentDetails;
                return $paymentDetails->transform(function ($paymentDetail) {
                    return $paymentDetail->toViewApi();
                });
            }),
            'transaction'      => $this->relationValidation('transaction', function () {
                $transaction = $this->transaction;
                return $transaction->toViewApi();
            }),
            'patient'      => $this->relationValidation('patient', function () {
                $patient = $this->patient;
                return $patient->toViewApi();
            }),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'childs'           => $this->relationValidation('childs', function () {
                $childs = $this->childs;
                return $childs->transform(function ($child) {
                    return $child->toViewApi();
                });
            }),
            'payment_summaries' => $this->relationValidation('paymentSummaries', function () {
                return $this->paymentSummaries->transform(function ($paymentSummary) {
                    return $paymentSummary->toViewApi();
                });
            })
        ];
        if (isset($this->pre_total_debt)) {
            $arr = $this->mergeArray($arr, [
                "pre_total_debt"       => $this->pre_total_debt,
                "pre_total_additional" => $this->pre_total_additional,
                "pre_total_discount"   => $this->pre_total_discount,
            ]);
        }

        return $arr;
    }
}
