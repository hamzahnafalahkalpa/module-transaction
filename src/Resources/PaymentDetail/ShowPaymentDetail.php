<?php

namespace Zahzah\ModuleTransaction\Resources\PaymentDetail;

use Zahzah\LaravelSupport\Resources\ApiResource;
use Zahzah\ModuleTransaction\Resources\TransactionItem\ShowTransactionItem;

class ShowPaymentDetail extends ViewPaymentDetail{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'transaction_item' => $this->relationValidation('transactionItem',function(){
                $transactionItem = $this->transactionItem;
                return $transactionItem->toShowApi();
            }),
            'paymentHistory'  => $this->relationValidation("paymentHistory", function() {
                return $this->paymentHistory->toShowApi();
            }),
            'payment_history'  => $this->relationValidation("paymentHistory", function() {
                return $this->paymentHistory->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        
        return $arr;
    }
}
