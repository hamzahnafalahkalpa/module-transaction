<?php

namespace Zahzah\ModuleTransaction\Schemas;

use Zahzah\LaravelSupport\Supports\PackageManagement;
use Zahzah\ModuleTransaction\Contracts\Invoice as ContractsInvoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Invoice extends PackageManagement implements ContractsInvoice{
    protected array $__guard   = ['id', 'author_id', 'author_type','consument_id', 'consument_type']; 
    protected array $__add     = ['invoice_code'];
    protected string $__entity = 'Invoice';

    public function prepareStoreInvoice(? array $attributes = null): Model{
        $attributes ??= request()->all();

        if (isset($attributes['id'])){
            $guard = ['id' => $attributes['id']];
        }else{
            $guard = [
                'consument_id'   => $attributes['consument_id'],
                'consument_type' => $attributes['consument_type']         
            ];
        }

        $add = [
            'author_type'    => $attributes['author_type'] ?? null,
            'author_id'      => $attributes['author_id'] ?? null        
        ];

        $invoice = $this->invoice()->updateOrCreate($guard,$add);

        //WHEN BILLING TRIGGERED
        if (isset($attributes['billing_at'])){
            $invoice->billing_at = $attributes['billing_at'];
            $invoice->save();
            $invoice->refresh();

            $billing_deferred        = $this->BillingDeferredModel()->find($invoice->getKey());
            $transaction             = $billing_deferred->transaction()->firstOrCreate();
            $payment_summary_billing = $billing_deferred->paymentSummary;
            if (isset($attributes['payment_summaries'])){
                $new_invoice = $this->InvoiceModel()->where('consument_id',$invoice->consument_id)
                                    ->where('consument_type',$invoice->consument_type)
                                    ->draft()->first();
                $invoice->load([
                    'paymentSummaries' => fn($q) => $q->whereNotIn('id',$attributes['payment_summaries'])
                ]);

                foreach ($invoice->paymentSummaries as $payment_summary) {
                    $previous_payment_summary   = $payment_summary->parent; 
                    $payment_summary->parent_id = $new_invoice->paymentSummary->getKey();
                    $payment_summary->save();
                }

                $invoice->load([
                    'paymentSummaries' => fn($q) => $q->whereIn('id',$attributes['payment_summaries'])
                ]);

                foreach ($invoice->paymentSummaries as $payment_summary) {
                    $transaction_item = $payment_summary->transactionItem()->firstOrCreate([
                        'item_id'        => $payment_summary->reference_id,
                        'item_type'      => $payment_summary->reference_type,
                        'item_name'      => $payment_summary->name,
                        'transaction_id' => $transaction->getKey()
                    ]);

                    $payment_detail = $transaction_item->paymentDetail()->firstOrCreate([
                        'payment_summary_id'  => $payment_summary_billing->getKey(),
                        'transaction_item_id' => $transaction_item->getKey()
                    ],[
                        'cogs'                => $payment_summary->total_cogs ?? 0,
                        'tax'                 => $payment_summary->total_tax ?? 0,
                        'additional'          => $payment_summary->total_additional,
                        'amount'              => $payment_summary->total_amount,
                        'debt'                => $payment_summary->total_debt,
                        'price'               => $payment_summary->total_amount
                    ]);
                }
            }
        }

        //FOR SETTLED ONLY
        if (isset($attributes['paid_at'])){
            $invoice->paid_at = $attributes['paid_at'];
            $invoice->save();
        }

        return $invoice;
    }


    public function storeInvoice(): array{
        return $this->transaction(function(){
            return $this->prepareStoreInvoice();
        });
    }

    protected function invoice(mixed $conditionals = null): Builder{
        return $this->InvoiceModel()->conditionals($conditionals);
    }

    // public function get(mixed $conditionals = null): Collection{
    //     return $this->invoice($conditionals)->get();
    // }

}
