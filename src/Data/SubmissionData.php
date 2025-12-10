<?php

namespace Hanafalah\ModuleTransaction\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleTransaction\Contracts\Data\SubmissionData as DataSubmissionData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class SubmissionData extends Data implements DataSubmissionData
{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public string $name;
    
    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('status')]
    #[MapName('status')]
    public ?string $status = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}