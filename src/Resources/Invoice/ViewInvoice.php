<?php

namespace Hanafalah\ModuleTransaction\Resources\Invoice;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewInvoice extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'invoice_code'      => $this->invoice_code,
            'generated_at'      => $this->generated_at,
            'billing_at'        => $this->billing_at,
            'paid_at'           => $this->paid_at,
            'created_at'        => $this->created_at,
            'transaction_billing_deferred' => $this->relationValidation('transactionBillingDeferred', function () {
                return $this->transactionBillingDeferred->toViewApi();
            }),
            'payment_summary'   => $this->relationValidation('paymentSummary', function () {
                return $this->paymentSummary->toViewApi();
            }),
            'author'            => $this->relationValidation('author', function () {
                $author = $this->author;
                return [
                    'id'    => $author->id,
                    'name'  => $author->name,
                ];
            }),
            'consument'         => $this->relationValidation('consument', function () {
                $consument = $this->consument;
                return [
                    'id'    => $consument->id,
                    'name'  => $consument->name,
                ];
            }),
            'total_debt' => $this->total_debt ?? 0,
            'total_paid' => $this->total_paid ?? 0
        ];

        return $arr;
    }
}
