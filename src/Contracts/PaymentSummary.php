<?php

namespace Hanafalah\ModuleTransaction\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\DataManagement;

interface PaymentSummary extends DataManagement
{
    public function prepareViewPaymentSummaryList(mixed $flags = null): Collection;
    public function preparePaymentSummary(mixed $flags = null);
    public function prepareViewPaymentSummaryPaginate(mixed $flags = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewPaymentSummaryPaginate(mixed $flags = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array;
    public function viewPaymentSummaryList(mixed $flags = null): array;
    public function getPaymentSummary(): mixed;
    public function addOrChange(?array $attributes = []): self;
    public function get(mixed $conditionals = null): Collection;
}
