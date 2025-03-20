<?php

namespace Zahzah\ModuleTransaction\Resources\SplitBill;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewSplitBill extends ApiResource
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
            'id'                => $this->id,
            'split_bill_code'   => $this->split_bill_code,
            'payment_method'    => $this->payment_method,
            'total_paid'        => $this->total_paid,
            'note'              => $this->note,
            'payer'             => $this->relationValidation('payer',function(){
                return $this->payer->toViewApi();
            }),
            'payment_summary'   => $this->relationValidation('paymentSummary',function(){
                return $this->paymentSummary->toViewApi();
            }),
            'payment_history' => $this->relationValidation('paymentHistory',function(){
                return $this->paymentHistory->toViewApi();
            }),
            'payment_details' => $this->getPaymentDetails(),
            // 'payment_details' => $this->relationValidation('paymentHistoryDetails',function(){
            //     return $this->paymentHistoryDetails->transform(function($payment_history){
            //         return $payment_history->toViewApi();
            //     });
            // }),
            // 'payment_history_details' => $this->relationValidation('paymentHistoryDetails',function(){
            //     return $this->paymentHistoryDetails->transform(function($payment_history){
            //         return $payment_history->toViewApi();
            //     });
            // }),
            'created_at'      => $this->created_at,
        ];
        
        return $arr;
    }
}
