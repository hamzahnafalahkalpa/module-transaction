<?php

namespace Hanafalah\ModuleTransaction\Resources\Invoice;

use Hanafalah\ModuleTransaction\Resources\PaymentSummary\ShowPaymentSummary;

class ShowInvoice extends ViewInvoice
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
            'payment_summary'   => $this->relationValidation('paymentSummary', function () {
                $paymentSummary = $this->paymentSummary;
                return new ShowPaymentSummary($paymentSummary);
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
