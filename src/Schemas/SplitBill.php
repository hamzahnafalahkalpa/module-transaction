<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\SplitBill as ContractsSplitBill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Hanafalah\ModuleTransaction\Resources\SplitBill\ViewSplitBill;

class SplitBill extends PackageManagement implements ContractsSplitBill
{
    protected string $__entity = 'SplitBill';
    public static $split_bill_model;

    protected array $__resources = [
        'view' => ViewSplitBill::class,
        'show' => ViewSplitBill::class,
    ];

    protected function showUsingRelation(): array
    {
        return [
            "paymentHistory.paymentHistoryDetails"
        ];
    }

    public function prepareStoreSplitBill(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (!isset($attributes['billing_id'])) throw new \Exception('billing_id is required');
        $payment_method = $this->PaymentMethodModel()->where("name", $attributes['payment_method'])->first();

        if (isset($attributes['payer_type']) && !isset($attributes['invoice_id'])) {
            $payer = $this->{$attributes['payer_type'] . 'Model'}()->findOrFail($attributes['payer_id']);
            if ($payer->getMorphClass() == $this->PayerModelMorph()) {
                $payer = $this->{$payer->flag . 'Model'}()->findOrFail($attributes['payer_id']);
            }
            $invoice_id = $payer->invoice()->firstOrCreate()->getKey();
            $attributes['invoice_id'] = $invoice_id;
        }

        $invoice = $this->InvoiceModel()->draft()->find($invoice_id);

        $add = [
            'billing_id'     => $attributes['billing_id'],
            'payment_method' => $attributes['payment_method'],
            'total_paid'     => $attributes['total_paid'] ?? 0,
            'payer_type'     => $attributes['payer_type'] ?? null,
            'payer_id'       => $attributes['payer_id']   ?? null,
            'invoice_id'     => $attributes['invoice_id'] ?? null
        ];

        if (isset($attributes['id'])) {
            $guard      = ['id' => $attributes['id']];
            $split_bill = $this->SplitBillModel()->updateOrCreate($guard, $add);
        } else {
            $split_bill = $this->SplitBillModel()->create($add);
        }
        $split_bill->paid_money = $attributes['paid_money'] ?? 0;
        $split_bill->cash_over  = $attributes['cash_over'] ?? 0;
        $split_bill->note       = $attributes['note'] ?? null;

        $transaction_split_bill = $split_bill->transaction;
        $billing = $attributes['billing'] ?? $split_bill->billing;
        $transaction_billing    = $billing->transaction;
        $transaction_split_bill->parent_id = $transaction_billing->getKey();
        $transaction_split_bill->save();

        if (isset($item['bank_id'])) {
            $bank = $this->BankModel()->findOrFail($item['bank_id']);
            $split_bill->bank_id = $bank->getKey();
            $split_bill->bank    = [
                'id'             => $bank->getKey(),
                'name'           => $bank->name,
                'account_name'   => $bank->account_name,
                'account_number' => $bank->account_number
            ];
        }
        if (isset($attributes['payment_method_detail'])) {
            $this->paymentMethodProp($attributes, $payment_method, $split_bill);
        }

        if (isset($attributes['payment_summaries']) && count($attributes['payment_summaries']) > 0) {
            $payment_history = $this->schemaContract('payment_history')->prepareStorePaymentHistory([
                'payment_method'    => $attributes['payment_method'],
                'split_bill'        => $split_bill,
                'billing'           => $billing,
                'parent_id'         => $invoice->paymentSummary()->firstOrCreate()->getKey(),
                'paid_money'        => $payment_method->name == 'DITAGIHKAN' ? 0 : $split_bill->paid_money,
                'cash_over'         => $split_bill->cash_over ?? 0,
                'transaction_id'    => $split_bill->transaction->getKey(),
                'reference_type'    => $split_bill->getMorphClass(),
                'reference_id'      => $split_bill->getKey(),
                'payment_summaries' => $attributes['payment_summaries'] ?? [],
                'payment_details'   => $attributes['payment_details'] ?? [],
                'discount'          => $attributes['discount'] ?? 0,
                'note'              => $attributes['note'] ?? null,
                'vouchers'          => $attributes['vouchers'] ?? []
            ]);
            if (!isset($attributes['total_paid'])) {
                $split_bill->total_paid   = $payment_history->total_paid;
                $billing->total_paid    ??= 0;
                $billing->total_debt    ??= 0;
                $billing->total_gross   ??= 0;
                $billing->total_net     ??= 0;
                $billing->total_paid     += $split_bill->total_paid;
                $billing->total_debt     += $payment_history->total_debt;
                $billing->total_gross    += $payment_history->gross;
                $billing->total_net      += $payment_history->total_net;
            }
        } else {
            throw new \Exception('payment_summaries are required');
        }

        $split_bill->save();
        $split_bill->reporting();
        $billing->save();
        return static::$split_bill_model = $split_bill;
    }

    private function paymentMethodProp($attributes, $payment_method, &$split_bill)
    {
        $split_bill_data = $attributes['payment_method_detail'];
        switch ($payment_method->name) {
            case "E-MONEY":
                $props = [
                    "phone_number"     => $split_bill_data['phone_number'] ?? null,
                    "transaction_code" => $split_bill_data['transaction_code'] ?? null,
                ];
                break;
            case "CREDIT CARD":
                $props = [
                    "card_number"     => $split_bill_data['card_number'] ?? null,
                    "card_type"       => $split_bill_data['card_type'] ?? null,
                    "card_expiry"     => $split_bill_data['card_expiry'] ?? null,
                    "card_tran_code"  => $split_bill_data['card_tran_code'] ?? null,
                    "bank"            => $split_bill_data['bank'] ?? null,
                ];
                break;
            case "CASH":
                $props = [];
                break;
            case "BANK TRANSFER":
                $props = [];
                break;
            case "DEBIT CARD":
                $props = [];
                break;
            default:
                $props = [];
        }

        $split_bill->setAttribute('payment_method_detail', $props);
    }

    public function prepareShowSplitBill(?Model $model = null): ?Model
    {
        $this->booting();
        $model ??= $this->getSplitBill();
        if (!isset($model)) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('id is required');
            $model = $this->splitBill()->with($this->showUsingRelation())->first();
        } else {
            $model->load($this->showUsingRelation());
        }

        return static::$split_bill_model = $model;
    }

    public function showSplitBill(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], $this->prepareShowSplitBill($model));
    }

    public function splitBill(mixed $conditionals = null): Builder
    {
        $this->booting();
        return $this->SplitBillModel()->withParameters()->conditionals($conditionals);
    }

    public function getSplitBill(): mixed
    {
        return static::$split_bill_model;
    }
}
