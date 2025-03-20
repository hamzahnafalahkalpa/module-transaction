<?php

use Zahzah\ModuleTransaction\Models as ModuleTransactionModels;
use Zahzah\ModuleTransaction\Commands as ModuleTransactionCommands;
use Zahzah\ModuleTransaction\Contracts;

return [
    'commands' => [
        ModuleTransactionCommands\InstallMakeCommand::class
    ],
    'contracts'  => [
        'consument'              => Contracts\Consument::class,
        'refund'                 => Contracts\Refund::class,
        'split_bill'             => Contracts\SplitBill::class,
        'billing'                => Contracts\Billing::class,
        'deposit'                => Contracts\Deposit::class,
        'invoice'                => Contracts\Invoice::class,
        'transaction'            => Contracts\Transaction::class,
        'transaction_item'       => Contracts\TransactionItem::class,
        'bank'                   => Contracts\Bank::class,
        'voucher'                => Contracts\Voucher::class,
        'voucher_rule'           => Contracts\VoucherRule::class,
        // 'voucher_transaction'    => Contracts\VoucherTransaction::class,
        // 'component_detail'       => Contracts\ComponentDetail::class,
        'price_component'        => Contracts\PriceComponent::class,
        'tariff_component'       => Contracts\TariffComponent::class,
        'payment_detail'         => Contracts\PaymentDetail::class,
        'payment_history'        => Contracts\PaymentHistory::class,
        'payment_history_detail' => Contracts\PaymentHistoryDetail::class,
        'payment_summary'        => Contracts\PaymentSummary::class,
        'payment_method'         => Contracts\PaymentMethod::class,
        'condition'              => Contracts\Voucher\Condition::class
    ],
    'database'   => [
        'models' => [
            'Consument'               => ModuleTransactionModels\Consument\Consument::class,
            'Refund'                  => ModuleTransactionModels\Transaction\Refund::class,
            'SplitBill'               => ModuleTransactionModels\Transaction\SplitBill::class,
            'BillingDeferred'         => ModuleTransactionModels\Transaction\BillingDeferred::class,
            'Billing'                 => ModuleTransactionModels\Transaction\Billing::class,
            'Deposit'                 => ModuleTransactionModels\Transaction\Deposit::class,
            'Invoice'                 => ModuleTransactionModels\Transaction\Invoice::class,
            'Transaction'             => ModuleTransactionModels\Transaction\Transaction::class,
            'TransactionHasConsument' => ModuleTransactionModels\Transaction\TransactionHasConsument::class,
            'TransactionItem'         => ModuleTransactionModels\Transaction\TransactionItem::class,
            'Bank'                    => ModuleTransactionModels\Price\Bank::class,
            'ModelHasVoucher'         => ModuleTransactionModels\Price\ModelHasVoucher::class,
            'Voucher'                 => ModuleTransactionModels\Price\Voucher::class,
            'VoucherRule'             => ModuleTransactionModels\Price\VoucherRule::class,
            'VoucherTransaction'      => ModuleTransactionModels\Price\VoucherTransaction::class,
            'ComponentDetail'         => ModuleTransactionModels\Price\ComponentDetail::class,
            'PriceComponent'          => ModuleTransactionModels\Price\PriceComponent::class,
            'TariffComponent'         => ModuleTransactionModels\Price\TariffComponent::class,
            'PaymentDetail'           => ModuleTransactionModels\Payment\PaymentDetail::class,
            'PaymentHistory'          => ModuleTransactionModels\Payment\PaymentHistory::class,
            'PaymentHistoryDetail'    => ModuleTransactionModels\Payment\PaymentHistoryDetail::class,
            'PaymentSummary'          => ModuleTransactionModels\Payment\PaymentSummary::class,
            'PaymentMethod'           => ModuleTransactionModels\Payment\PaymentMethod::class,
            'PaymentHistoryHasModel'  => ModuleTransactionModels\Payment\PaymentHistoryHasModel::class,

        ]
    ],
    'voucher' => [
        'benefit_targets' => [
            'benefit_billing'     => Contracts\Voucher\Benefit\Billing::class,
        ],
        'conditions' => [
            'maximum_usage'       => Contracts\Voucher\UsageCondition::class,
            'in_date_range'       => Contracts\Voucher\DateCondition::class,
            'less_than_date'      => Contracts\Voucher\DateCondition::class,
            'after_than_date'     => Contracts\Voucher\DateCondition::class,
            'minimum_transaction' => Contracts\Voucher\TransactionCondition::class
        ]
    ],
    'author' => \App\Models\User::class
];