<?php

namespace Hanafalah\ModuleTransaction\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleTransaction\Enums\Bank\Status;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class BankData extends Data
{
    public function __construct(
        #[MapInputName('id')]
        #[MapName('id')]
        public mixed $id = null,

        #[MapInputName('name')]
        #[MapName('name')]
        public string $name,

        #[MapInputName('account_number')]
        #[MapName('account_number')]
        public string $account_number,

        #[MapInputName('account_name')]
        #[MapName('account_name')]
        public string $account_name,

        #[MapInputName('status')]
        #[MapName('status')]
        public ?string $status = Status::ACTIVE->value
    ) {}
}
