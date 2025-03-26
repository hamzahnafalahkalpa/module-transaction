<?php

namespace Hanafalah\ModuleTransaction\Supports\Benefit;

use Hanafalah\ModuleTransaction\Contracts\Supports\Benefit\Benefit as ContractBenefit;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\Supports\Condition\Condition;

class Benefit extends Condition implements ContractBenefit
{
    protected $__benefit_target;
    protected $__benefit_format;
    protected $__benefit_value;
    protected $__benefit_type_value;
    protected $__max_benefit_value;

    protected function initializeBenefit(Model $voucher): self
    {
        $this->__benefit_target     = $voucher->benefit_target;
        $this->__benefit_format     = $voucher->benefit_format;
        $this->__benefit_value      = $voucher->benefit_value;
        $this->__benefit_type_value = $voucher->benefit_type_value;
        $this->__max_benefit_value  = $voucher->max_benefit_value;
        return $this;
    }

    protected function calculateBenefitByTypeValue(array &$attributes, &$model)
    {
        $prefix = (!isset($attributes['reported_at'])) ? 'pre_' : '';
        $payment_summary_prefix = '';
        $is_payment_summary = false;

        if (in_array($model->getMorphClass(), [$this->PaymentSummaryModelMorph(), $this->PaymentHistoryModelMorph()])) {
            $payment_summary_prefix = 'total_';
            $prefix .= 'total_';
            $is_payment_summary = true;
        }
        $model->{$prefix . 'amount'}           ??= $model->{$payment_summary_prefix . 'amount'} ?? 0;
        $model->{$prefix . 'debt'}             ??= $model->{$payment_summary_prefix . 'debt'} ?? 0;
        $model->{$prefix . 'additional'}       ??= $model->{$payment_summary_prefix . 'additional'} ?? 0;
        $model->{$prefix . 'discount'}         ??= $model->{$payment_summary_prefix . 'discount'} ?? 0;
        if ($model->getMorphClass() != $this->PaymentHistoryModelMorph()) {
            $model->{$prefix . 'current_amount'}     = $model->{$prefix . 'amount'};
            $model->{$prefix . 'current_debt'}       = $model->{$prefix . 'debt'};
            $model->{$prefix . 'current_additional'} = $model->{$prefix . 'additional'};
            $model->{$prefix . 'current_discount'}   = $model->{$prefix . 'discount'};
        }
        $total_debt        = $model->{$prefix . 'debt'};
        $total_additional  = $model->{$prefix . 'additional'};
        $total_discount    = $model->{$prefix . 'discount'};
        $price             = $model->price ?? $model->{$prefix . 'debt'};

        $benefit_in_rupiah = $this->calculateBenefitValue($price);
        switch ($this->__benefit_type_value) {
            case 'REPLACEMENT':
                if (!$is_payment_summary) {
                    $model->price       = $benefit_in_rupiah;
                    $benefit_in_rupiah *= $model->qty ?? 1;
                }
                $total_debt                = $benefit_in_rupiah;
                $model->{$prefix . 'amount'} = $total_debt;
                break;
            case 'DISCOUNT':
                if (!$is_payment_summary) $benefit_in_rupiah *= $model->qty ?? 1;
                $total_debt     = $total_debt - $benefit_in_rupiah;
                $total_debt     = ($total_debt < 0) ? 0 : $total_debt;
                $total_discount += $benefit_in_rupiah;
                break;
            case 'MARKUP':
                if (!$is_payment_summary) {
                    $model->price       = $total_debt + $benefit_in_rupiah;
                    $benefit_in_rupiah *= $model->qty ?? 1;
                }
                $total_debt        = $total_debt + $benefit_in_rupiah;
                $total_additional += $benefit_in_rupiah;
                $model->{$prefix . 'amount'} = $total_debt;
                break;
            case 'MARKDOWN':
                if (!$is_payment_summary) {
                    $model->price       = $total_debt - $benefit_in_rupiah;
                    $benefit_in_rupiah *= $model->qty ?? 1;
                }
                $total_debt  = $total_debt - $benefit_in_rupiah;
                $total_debt  = ($total_debt < 0) ? 0 : $total_debt;
                $model->{$prefix . 'amount'} = $total_debt;
                break;
        }
        $model->{$prefix . 'debt'}       = $total_debt;
        $model->{$prefix . 'additional'} = $total_additional;
        $model->{$prefix . 'discount'}   = $total_discount;

        if ($prefix != '') $model->prefix = $prefix;
        if (isset($attributes['reported_at'])) $model->save();
        return $model;
    }

    protected function calculateBenefitValue(int $total): int
    {
        if ($this->__benefit_type_value == 'REPLACEMENT') {
            if ($this->__benefit_format == 'PERCENTAGE') {
                throw new \Exception('PERCENTAGE not support for REPLACEMENT');
            }
            $benefit_in_rupiah = $this->__benefit_value;
        } else {
            $benefit_in_rupiah = ($this->__benefit_format == 'PERCENTAGE') ? $total * $this->__benefit_value / 100 : $this->__benefit_value;
            if (isset($this->__max_benefit_value) && $benefit_in_rupiah > $this->__max_benefit_value) {
                $benefit_in_rupiah = $this->__max_benefit_value;
            }
        }
        return $benefit_in_rupiah;
    }

    protected function updatePaymentHistoryRate(array &$attributes, Model &$payment_history, Model &$payment_summary, Model &$model)
    {
        // if (isset($attributes['reported_at'])){
        // $payment_summary->refresh();

        // $attr_payment_summary_ids = $this->pluckColumn($attributes['payment_summaries'],'id');
        // $search_payment_summary   = $this->searchArray($attr_payment_summary_ids,$payment_summary->getKey());
        // if (\is_numeric($search_payment_summary)){
        //     if ($this->PaymentSummaryModelMorph() == $payment_summary->getMorphClass()){
        //         $attributes['payment_summaries'][$search_payment_summary]['total_discount'] = $payment_summary->total_discount;
        //     }else{
        //         $attr_payment_summary = &$attributes['payment_summaries'][$search_payment_summary];
        //         $attr_payment_details_ids = $this->pluckColumn($attr_payment_summary['payment_details'],'id');
        //         $search_payment_detail    = $this->searchArray($attr_payment_details_ids, $model->getKey());
        //         if (\is_numeric($search_payment_detail)){
        //             $attr_payment_summary['payment_details'][$search_payment_detail]['total_discount'] = $model->total_discount;
        //         }
        //     }
        // }
        // }else{
        if (!isset($attributes['reported_at'])) {
            $rate_names = ['amount', 'debt', 'additional', 'discount'];
            $add_prefix = ($is_payment_summary = $model->getMorphClass() == $this->PaymentSummaryModelMorph()) ? 'total_' : '';
            foreach ($rate_names as $rate_name) {
                //CALCULATE BETWEEN ORIGINAL AND PRE VALUE
                $sub_result = ($model->{$model->prefix . $rate_name} ?? 0) - $model->{$model->prefix . 'current_' . $rate_name};
                if ($this->__benefit_type_value != 'DISCOUNT') {
                    //REPLACE REAL VALUE USING PRE VALUE
                    $model->{$add_prefix . $rate_name} = $model->{$model->prefix . $rate_name};

                    if (!$is_payment_summary) $payment_summary->{'total_' . $rate_name} += $sub_result;
                    $payment_history->{'total_' . $rate_name} += $sub_result;
                    unset($model->{$model->prefix . $rate_name});
                } else {
                    if ($rate_name == 'amount') {
                        unset($model->{$model->prefix . 'current_' . $rate_name});
                        continue;
                    }
                    if (!$is_payment_summary) {
                        $payment_summary->{'pre_total_' . $rate_name} ??= $payment_summary->{'total_' . $rate_name};
                        $payment_summary->{'pre_total_' . $rate_name}  += $sub_result;
                    }
                    $payment_history->{'pre_total_' . $rate_name} ??= $payment_history->{'total_' . $rate_name};
                    $payment_history->{'pre_total_' . $rate_name} += $sub_result;
                }
                unset($model->{$model->prefix . 'current_' . $rate_name});
            }
        }
        unset($model->prefix);
    }
}
