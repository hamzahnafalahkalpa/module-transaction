<?php

namespace Hanafalah\ModuleTransaction\Data;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\Data;

class SplitBillDTO extends Data
{
    public function __construct(
        public ?mixed $id,
        public mixed $billing_id,
        public string $payment_method,
        public int $total_paid,
        public ?string $payer_type,
        public int $payer_id,
        public mixed $invoice_id,
        public ?int $paid_money,
        public int $cash_over = 0,
        public ?Model $billing,
        public ?int $bank_id,
        ...$args
    ) {}
}
