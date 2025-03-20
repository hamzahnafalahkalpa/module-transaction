<?php

namespace Hanafalah\ModuleTransaction\Resources\VoucherTransaction;

class ShowVoucherTransaction extends ViewVoucherTransaction
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
            'voucher' => $this->relationValidation('voucher', function () {
                return $this->voucher->toShowApi();
            }),
            'consument' => $this->relationValidation('consument', function () {
                return $this->consument->toShowApi();
            }),
            'payment_history' => $this->relationValidation('paymentHistory', function () {
                return $this->paymentHistory->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
