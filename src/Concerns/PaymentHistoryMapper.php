<?php

namespace Zahzah\ModuleTransaction\Concerns;

use Zahzah\ModuleTransaction\Data\PaymentHistoryDTO;

trait PaymentHistoryMapper{
    protected function storePaymentHistoryMapper(? array $attributes = null): PaymentHistoryDTO{
        $valid_attributes = isset($attributes['id']) || (
            isset($attributes['transaction_id']) && isset($attributes['reference_type']) && isset($attributes['reference_id'])
        );
        if (!$valid_attributes) throw new \Exception('Payment history not valid');

        return new PaymentHistoryDTO(
            $attributes['id'] ?? null,
            $attributes['parent_id'] ?? null,
            $attributes['transaction_id'] ?? null,
            $attributes['reference_type'] ?? null,
            $attributes['reference_id'] ?? null,
            $attributes['total_amount'] ?? 0,
            $attributes['total_cogs'] ?? 0,
            $attributes['total_discount'] ?? 0,
            $attributes['total_debt'] ?? 0,
            $attributes['total_tax'] ?? 0,
            $attributes['total_additional'] ?? 0
        );
    }
}
