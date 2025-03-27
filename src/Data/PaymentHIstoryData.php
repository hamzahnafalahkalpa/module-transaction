<?php

namespace Hanafalah\ModuleTransaction\Data;

use Hanafalah\LaravelSupport\Supports\Data;

class PaymentHistoryData extends Data
{
    public function __construct(
        public mixed $id,
        public mixed $parent_id,
        public mixed $transaction_id,
        public ?string $reference_type,
        public mixed $reference_id,
        public int $total_amount = 0,
        public int $total_cogs = 0,
        public int $total_discount = 0,
        public int $total_debt = 0,
        public int $total_tax = 0,
        public int $total_additional = 0,
        ...$args
    ) {}
}
