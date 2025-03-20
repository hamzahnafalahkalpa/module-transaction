<?php

namespace Zahzah\ModuleTransaction\Resources\Voucher;

class ShowVoucher extends ViewVoucher
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
            'author'              => $this->relationValidation('employee',function(){
                return $this->author->toShowApi();
            }),
            'voucher_rules'       => $this->relationValidation('voucherRules',function(){
                return $this->voucherRules->transform(function($voucherRule){
                    return $voucherRule->toShowApi();
                });
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request),$arr);
        
        return $arr;
    }
}