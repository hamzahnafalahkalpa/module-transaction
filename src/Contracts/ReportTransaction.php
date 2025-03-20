<?php

namespace Zahzah\ModuleTransaction\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ReportTransaction extends Transaction
{
    public function prepareTransactionReportPaginate(mixed $cache_reference_type = null,? array $morphs = null, int $perPage = 10, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): LengthAwarePaginator;
    public function viewTransactionReportPaginate(mixed $cache_reference_type = null,? array $morphs = null, int $perPage = 10, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): array;
}