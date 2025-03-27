<?php

namespace Hanafalah\ModuleTransaction\Data;

use Carbon\Carbon;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleTransaction\Enums\Billing\Status;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\DataCollection;

class BillingData extends Data
{
    public function __construct(
        #[MapInputName('id')]
        #[MapName('id')]
        public mixed $id = null,

        #[MapInputName('uuid')]
        #[MapName('uuid')]
        public mixed $uuid = null,

        #[MapInputName('transaction_id')]
        #[MapName('transaction_id')]
        public mixed $transaction_id,

        #[MapInputName('author_type')]
        #[MapName('author_type')]
        public ?string $author_type = null,

        #[MapInputName('author_id')]
        #[MapName('author_id')]
        public ?string $author_id = null,

        #[MapInputName('cashier_type')]
        #[MapName('cashier_type')]
        public ?string $cashier_type = null,

        #[MapInputName('cashier_id')]
        #[MapName('cashier_id')]
        public mixed $cashier_id = null,

        #[MapInputName('status')]
        #[MapName('status')]
        public ?string $status = Status::DRAFT->value,

        #[MapInputName('split_bills')]
        #[MapName('split_bills')]      
        #[DataCollectionOf(SplitBillData::class)]
        public DataCollection $split_bills = [],

        #[MapInputName('reported_at')]
        #[MapName('reported_at')]
        public ?Carbon $reported_at = null
    ) {}
}
