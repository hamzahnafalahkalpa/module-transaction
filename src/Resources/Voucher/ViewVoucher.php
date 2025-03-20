<?php

namespace Hanafalah\ModuleTransaction\Resources\Voucher;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewVoucher extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                  => $this->id,
            'name'                => $this->name,
            'status'              => $this->status,
            'benefit_target'      => $this->benefit_target,
            'benefit_format'      => $this->benefit_format,
            'benefit_value'       => $this->benefit_value,
            'benefit_type_value'  => $this->benefit_type_value,
            'max_benefit_value'   => $this->max_benefit_value,
            'is_auto_implement'   => ($this->is_auto_implement ?? 0) == 1,
            'author'              => $this->relationValidation('employee', function () {
                return $this->author->toViewApi();
            }),
            'voucher_rules'       => $this->relationValidation('voucherRules', function () {
                return $this->voucherRules->transform(function ($voucherRule) {
                    return $voucherRule->toViewApi();
                });
            })
        ];

        $props = $this->getPropsData() ?? [];
        foreach ($props as $key => $prop) {
            $arr[$key] = $prop;
        }

        return $arr;
    }
}
