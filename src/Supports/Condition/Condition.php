<?php

namespace Hanafalah\ModuleTransaction\Supports\Condition;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\Contracts\Voucher\Condition as ConditionInterface;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Illuminate\Support\Str;
use Hanafalah\ModuleTransaction\Concerns\PaymentCalculation;

class Condition extends PackageManagement implements ConditionInterface
{
    use PaymentCalculation;

    protected static $__split_bill_model;
    protected static $__payment_history;
    protected static $__transaction_model;
    protected static $__transaction;
    protected static array $__benefit_conditions = [];
    protected static $__payment_summaries = [];
    protected static $__payment_details   = [];
    protected static $__total_debt       = 0;
    protected static $__total_discount   = 0;
    protected static $__total_additional = 0;
    protected static $__voucher;
    private $__registered_transactions = [];
    private ?Model $__payment_transaction;

    public function setupTransaction(array &$attributes)
    {
        if (isset($attributes['split_bill_id'])) {
            static::$__split_bill_model   = $this->SplitBillModel()->findOrFail($attributes['split_bill_id']);
            $split_bill                   = &static::$__split_bill_model;
            static::$__payment_history    = $split_bill->paymentHistory;
            $payment_history              = &static::$__payment_history;
            // $attributes['transaction_id'] = $split_bill->transaction->getKey();
        } else {
            static::$__payment_history = $this->PaymentHistoryModel();
            $payment_history = &static::$__payment_history;
        }
        static::$__payment_history->setAttribute('prop_vouchers', []);
        static::$__transaction = $this->getTransaction($attributes, $payment_history);
        $attributes['transaction_id'] = static::$__transaction->getKey();
    }

    protected function hasPaymentSummaries(): bool
    {
        return isset(static::$__payment_summaries) && count(static::$__payment_summaries) > 0;
    }

    public function validation(Model &$voucher, array &$attributes): array
    {
        $transaction                  = static::$__transaction;
        $attributes['transaction_id'] = $transaction->getKey();
        $voucher_rules                = &$voucher->voucherRules;

        $voucher->is_valid = true;
        foreach ($voucher_rules as &$voucher_rule) {
            $voucher_rule->is_valid ??= true;
            $condition_schema         = app(config('module-transaction.voucher.conditions.condition_' . Str::snake(Str::lower($voucher_rule->condition))));
            $voucher_rule->is_valid  &= $condition_schema->check($voucher_rule, $attributes);
            if ($voucher->is_valid) $voucher->is_valid = $voucher_rule->is_valid;
        }
        if ($voucher->is_valid) {
            static::$__voucher = $voucher;
            if (isset($attributes['reported_at'])) {
                $voucher_transaction = $this->createVoucherTransaction($attributes, $transaction, $voucher);
            }
            if (isset(static::$__payment_history)) {
                static::$__payment_history = $this->calculatingBenefit(static::$__payment_history, $voucher, $attributes);
                $prop_vouchers ??= static::$__payment_history['prop_vouchers'];
                $prop_vouchers[] = [
                    'id'   => $voucher->getKey(),
                    'name' => $voucher->name
                ];
                static::$__payment_history->setAttribute('prop_vouchers', $prop_vouchers);
                if (isset(static::$__payment_history->id)) {
                    static::$__payment_history->save();
                }
            }
        }
        static::$__benefit_conditions['services'] = [];
        return [$voucher, static::$__payment_history];
    }

    public function getPaymentHistory()
    {
        return static::$__payment_history;
    }

    protected function calculatingBenefit(&$payment_history, &$voucher, &$attributes)
    {
        $benefit_schema  = app(config('module-transaction.voucher.benefit_targets.benefit_' . Str::snake(Str::lower($voucher->benefit_target))));
        return $benefit_schema->calculating($payment_history, $voucher, $attributes);
    }

    private function createVoucherTransaction($attributes, &$transaction, &$voucher)
    {
        $voucher_transaction = $transaction->voucherTransaction()->updateOrCreate([
            'ref_transaction_id'  => $transaction->getKey(),
            'voucher_id'          => $voucher->getKey(),
            'consument_type'      => $attributes['consument_type'] ?? null,
            'consument_id'        => $attributes['consument_id'] ?? null,
        ], [
            'payment_history_id'  => $attributes['payment_history_id'] ?? null,
            'name'                => $voucher->name,
            'reported_at'         => $attributes['reported_at'] ?? null
        ]);

        $new_transaction = &$voucher_transaction->transaction;
        $new_transaction->parent_id = $transaction->getKey();
        $new_transaction->save();

        $new_transaction->setRelation('reference', $voucher_transaction);
        $new_transaction->setRelation('parent', $transaction);
        $voucher->setRelation('voucherTransaction', $voucher_transaction);
        return $voucher_transaction;
    }

    private function getTransaction($attributes, &$payment_history)
    {
        static::$__transaction_model = $this->TransactionModel()->findOrFail($attributes['transaction_id']);
        $transaction = &static::$__transaction_model;
        $transaction->setRelation('childs', new Collection);
        $prop_summaries = new Collection;
        $payment_history->syncOriginal();
        $payment_history->total_amount    = 0;
        $payment_history->total_aditional = 0;
        $payment_history->total_discount  = 0;
        $payment_history->total_debt      = 0;
        foreach ($attributes['payment_summaries'] as $payment_summary) {
            //GET TRANSACTION FROM PAYMENT SUMMARY
            $this->findTransaction($payment_summary, $transaction);
            $payment_transaction = &$this->__payment_transaction;

            $payment_summary_model = $payment_transaction->paymentSummary;

            if (isset($payment_summary['payment_details']) && count($payment_summary['payment_details']) > 0) {
                $payment_detail_ids = $this->pluckColumn($payment_summary['payment_details'], 'id');
                $payment_summary_model->load([
                    'paymentDetails' => function ($query) use ($payment_detail_ids) {
                        $query->whereIn('id', $payment_detail_ids);
                    }
                ]);

                $amounts = $debts = $additionals = $discounts = 0;
                foreach ($payment_summary_model->paymentDetails as $payment_detail) {
                    static::$__payment_details[] = &$payment_detail;
                    $amounts                    += $payment_detail->amount;
                    static::$__total_debt       += $payment_detail->debt;
                    $debts                      += $payment_detail->debt;
                    static::$__total_additional += $payment_detail->additional;
                    $additionals                += $payment_detail->additional;
                    static::$__total_discount   += $payment_detail->discount;
                    $discounts                  += $payment_detail->discount;
                }

                $payment_summary_model->total_amount    = $this->rounding($amounts);
                $payment_summary_model->total_aditional = $additionals;
                $payment_summary_model->total_discount  = $discounts;
                $payment_summary_model->total_debt      = $this->rounding($debts);

                $payment_history->total_amount          += $this->rounding($amounts);
                $payment_history->total_aditional       += $additionals;
                $payment_history->total_discount        += $discounts;
                $payment_history->total_debt            += $this->rounding($debts);
            }
            static::$__payment_summaries[] = $payment_summary_model;
            $prop_summaries->push($payment_summary_model);
        }

        static::$__total_debt = $this->rounding(static::$__total_debt);
        $payment_history->setRelation('paymentSummaries', $prop_summaries);
        return $transaction;
    }

    private function findTransaction(array $payment_summary, Model &$transaction)
    {
        $payment_summary_model = $this->PaymentSummaryModel()->findOrFail($payment_summary['id']);
        $payment_transaction   = $payment_summary_model->transaction()->firstOrFail();

        $payment_transaction = $this->TransactionModel()->whereHas('paymentSummary', function ($query) use ($payment_summary) {
            $query->where('id', $payment_summary['id']);
        })->firstOrFail();

        $search = $this->findTransactionHasRegistered($payment_transaction->getKey());
        if (is_numeric($search)) {
            $payment_transaction = &$transaction->childs[$search];
        } else {
            if ($payment_transaction->getKey() == $transaction->getKey()) {
                //IF TRANSACTION IS SAME AS $transaction
                $payment_transaction = &$transaction;
            } else {
                //FOR NEW TRANSACTION, ADD IT INTO CHILDS OF $transaction
                $this->__registered_transactions[] = $payment_transaction->getKey();
                $transaction->childs->push($payment_transaction);
                $this->__payment_transaction = &$transaction->childs[count($transaction->childs) - 1];
            }
        }
        $this->__payment_transaction->setRelation('paymentSummary', $payment_summary_model);
    }

    private function findTransactionHasRegistered(mixed $transaction_id): mixed
    {
        return $this->searchArray($this->__registered_transactions, $transaction_id);
    }
}
