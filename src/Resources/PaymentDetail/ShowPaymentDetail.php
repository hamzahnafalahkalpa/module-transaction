<?php

namespace Hanafalah\ModuleTransaction\Resources\PaymentDetail;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModuleTransaction\Resources\TransactionItem\ShowTransactionItem;

class ShowPaymentDetail extends ViewPaymentDetail
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
            'transaction_item' => $this->relationValidation('transactionItem', function () {
                $transactionItem = $this->transactionItem;
                return $transactionItem->toShowApi();
            }),
            'paymentHistory'  => $this->relationValidation("paymentHistory", function () {
                return $this->paymentHistory->toShowApi();
            }),
            'payment_history'  => $this->relationValidation("paymentHistory", function () {
                return $this->paymentHistory->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
