<?php

namespace Zahzah\ModuleTransaction\Resources\VoucherRule;

class ShowVoucherRule extends ViewVoucherRule
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
            'voucher'             => $this->relationValidation('voucher', function() {
                return $this->voucher->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request),$arr);
        
        return $arr;
    }
}
