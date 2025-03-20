<?php

namespace Hanafalah\ModuleTransaction\Resources\PaymentSummary;

use Hanafalah\ModuleTransaction\Resources\PaymentDetail\ShowPaymentDetail;
use Hanafalah\ModuleTransaction\Resources\Transaction\ShowTransaction;

class ShowPaymentSummary extends ViewPaymentSummary
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
            'name'             => $this->name ?? null,
            'reference_type'   => $this->reference_type,
            'transaction'      => $this->relationValidation('transaction', function () {
                return $this->transaction->toShowApi();
            }),
            'total_amount'     => $this->total_amount,
            'total_debt'       => $this->total_debt,
            'total_tax'        => $this->total_tax,
            'total_additional' => $this->total_additional,
            'payment_details'  => $this->relationValidation('paymentDetails', function () {
                $paymentDetails = $this->paymentDetails;
                return $paymentDetails->transform(function ($paymentDetail) {
                    $paymentDetail->load('paymentHistory');
                    return $paymentDetail->toShowApi();
                });
            }),
            'childs' => $this->relationValidation('childs', function () {
                $childs = $this->childs;
                return $childs->transform(function ($child) {
                    return $child->toShowApi();
                });
            }),
            'payment_summaries' => $this->relationValidation('paymentSummaries', function () {
                return $this->paymentSummaries->transform(function ($paymentSummary) {
                    return $paymentSummary->toShowApi();
                });
            })
        ];

        if ($this->relationLoaded('recursiveChilds')) {
            $arr['childs'] = $this->relationValidation('recursiveChilds', function () {
                $childs = $this->recursiveChilds;
                return $childs->transform(function ($child) {
                    return $child->toShowApi();
                });
            });
        }

        if ($this->relationLoaded('recursiveInvoiceChilds')) {
            $arr['childs'] = $this->relationValidation('recursiveInvoiceChilds', function () {
                $childs = $this->recursiveInvoiceChilds;
                return $childs->transform(function ($child) {
                    return $child->toShowApi();
                });
            });
        }


        $props = $this->getPropsData() ?? [];
        foreach ($props as $key => $prop) {
            $arr[$key] = $prop;
        }
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
