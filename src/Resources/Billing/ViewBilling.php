<?php

namespace Zahzah\ModuleTransaction\Resources\Billing;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewBilling extends ApiResource
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
            'uuid'           => $this->uuid,
            'id'             => $this->id,
            'billing_code'   => $this->billing_code,
            'transaction_id' => $this->transaction_id,
            'author'         => $this->relationValidation('author',function(){
                return $this->author->toViewApi();
            }),
            'cashier'        => $this->relationValidation('cashier',function(){
                return $this->cashier->toViewApi();
            }),
            'transaction'  => $this->relationValidation('hasTransaction',function() {
                    return $this->hasTransaction->toShowApi();
            }),
            'transaction_billing' => $this->relationValidation('transaction',function() {
                return $this->transaction->toShowApi();
            }),
            'total_debt'     => $this->total_debt ?? 0,
            'total_amount'   => $this->total_amount ?? 0,
            'reported_at'    => $this->reported_at,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at
        ];
        
        return $arr;
    }
}
