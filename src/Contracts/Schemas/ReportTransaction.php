<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Data\PaginateData;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReportTransaction extends Transaction
{
    public function prepareViewTransactionReportPaginate(mixed $cache_reference_type = null, ?array $morphs = null, PaginateData $paginate_dto): LengthAwarePaginator;
    public function viewTransactionReportPaginate(mixed $cache_reference_type = null, ?array $morphs = null, ? PaginateData $paginate_dto = null): array;
}
