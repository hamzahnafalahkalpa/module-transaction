<?php

namespace Hanafalah\ModuleTransaction\Resources\Transaction;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewTransaction extends ApiResource
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
            'uuid'              => $this->uuid,
            'transaction_code'  => $this->transaction_code,
            'reference_type'    => $this->reference_type,
            'reference'         => $this->relationValidation('reference', function () {
                $reference = $this->reference;
                return $reference->toViewApi();
            }),
            // 'medic_service'     => $this->relationValidation('reference', function () {
            //     $reference = $this->reference;
            //     return $reference->relationValidation('visitRegistration', function () use ($reference) {
            //         $visitRegistration = $reference->visitRegistration;
            //         return [
            //             'name'  => $visitRegistration->medic_service
            //         ];
            //     });
            // }),
            'payment_summary' => $this->relationValidation('paymentSummary', function () {
                return $this->paymentSummary->toViewApi();
                // return new ShowPaymentSummary($this->paymentSummary);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        $props = $this->getPropsData() ?? [];
        foreach ($props as $key => $prop) {
            $arr[$key] = $prop;
        }

        return $arr;
    }
}
