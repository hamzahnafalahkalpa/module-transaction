<?php

namespace Zahzah\ModuleTransaction\Data;

use Carbon\Carbon;
use Zahzah\LaravelSupport\Supports\Data;

class BillingDTO extends Data{
    public function __construct(
        public ?mixed $id,
        public mixed $transaction_id,
        public ?Carbon $reported_at,
        public ?string $author_type,
        public mixed $author_id,
        public ?string $cashier_type,
        public mixed $cashier_id,
        public ?array $split_bills,
        ...$args
    ){}    
}