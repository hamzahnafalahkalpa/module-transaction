<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};
use Illuminate\Support\Str;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Concerns\PaymentCalculation;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Voucher as ContractsVoucher;
use Hanafalah\ModuleTransaction\Enums\Voucher\Status;
use Hanafalah\ModuleTransaction\Resources\Voucher\{ViewVoucher, ShowVoucher};

class Voucher extends PackageManagement implements ContractsVoucher
{
    use PaymentCalculation;

    protected string $__entity = 'voucher';
    public static $voucher_model;

    protected array $__resources = [
        'view' => ViewVoucher::class,
        'show' => ShowVoucher::class
    ];

    public function showUsingRelation(): array
    {
        return [
            'voucherRules'
        ];
    }

    public function getVoucher(): mixed
    {
        return static::$voucher_model;
    }

    public function prepareStoreVoucher(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (isset($attributes['author_id'])) {
            $author = app(config('module-transaction.voucher.author'))->findOrFail($attributes['author_id']);
            $attributes['author_type'] = $author->getMorphClass();
        }

        $benefit_targets = config('module-transaction.voucher.benefit_targets');
        $benefit_target = 'benefit_' . Str::snake(Str::lower($attributes['benefit_target']));
        if (!isset($benefit_targets[$benefit_target])) throw new \Exception('Benefit target not found');

        $voucher = $this->VoucherModel();
        $model = $voucher->withoutGlobalScope('voucher-status')->updateOrCreate([
            'id' => $attributes['id'] ?? null
        ], [
            'name'               => $attributes['name'],
            'benefit_target'     => Str::upper($attributes['benefit_target']),
            'benefit_format'     => $attributes['benefit_format'],
            'benefit_value'      => $attributes['benefit_value'] ?? 0,
            'benefit_type_value' => $attributes['benefit_type_value'] ?? $voucher::BENEFIT_TYPE_PERCENTAGE,
            'max_benefit_value'  => $attributes['max_benefit_value'] ?? null,
            'is_auto_implement'  => $attributes['is_auto_implement'] ?? false,
            'author_type'        => $attributes['author_type'] ?? null,
            'author_id'          => $attributes['author_id'] ?? null,
        ]);

        if (isset($attributes['voucher_rules']) && is_array($attributes['voucher_rules']) && count($attributes['voucher_rules']) > 0) {
            $props = [];
            $voucher_rule_schema = $this->schemaContract('voucher_rule');
            $keep = [];
            foreach ($attributes['voucher_rules'] as $voucher_rule) {
                $voucher_rule['voucher_id'] = $model->getKey();
                $voucher_rule_model = $voucher_rule_schema->prepareStoreVoucherRule($voucher_rule);
                $keep[]  = $voucher_rule_model->getKey();
                $props[] = [
                    "id"         => $voucher_rule_model->getKey(),
                    "name"       => $voucher_rule_model->name,
                    "condition"  => $voucher_rule_model->condition,
                    "rule"       => $voucher_rule_model->rule
                ];
            }
            $model->setAttribute('voucher_rules', $props);
            $model->voucherRules()->whereNotIn('id', $keep)->delete();
        }
        $model->save();
        return static::$voucher_model = $model;
    }

    public function storeVoucher(): array
    {
        return $this->transaction(function () {
            return $this->showVoucher($this->prepareStoreVoucher());
        });
    }

    public function prepareShowVoucher(?Model $model = null, ?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $model ??= $this->getVoucher();
        if (isset($attributes['id'])) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Id not found');
            $model = $this->voucher()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return static::$voucher_model = $model;
    }

    public function showVoucher(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowVoucher($model);
        });
    }

    public function prepareRevalidateVoucher(mixed $vouchers, array &$attributes): mixed
    {
        if (isset($attributes['split_bill_id']) || isset($attributes['transaction_id'])) {
            $condition_schema = $this->schemaContract('condition');
            $condition_schema->setupTransaction($attributes);
            foreach ($vouchers as &$voucher) {
                list($voucher, $payment_history) = $condition_schema->validation($voucher, $attributes);
            }
            $payment_history = $condition_schema->getPaymentHistory();
            $payment_history->total_debt   = $this->rounding($payment_history->total_debt);
            $payment_history->total_amount = $this->rounding($payment_history->total_amount);
        }
        return [$vouchers, $payment_history ?? null];
    }

    public function prepareViewVoucherList(?array $attributes = null): Collection
    {
        $attributes ??= request()->all();
        static::$voucher_model = $vouchers = $this->voucher()
            ->when(isset(request()->available_voucher), function ($query) {
                $query->whereNull('is_auto_implement');
            })->with('voucherRules')->orderBy('name', 'asc')->get();
        list($voucher) = $this->prepareRevalidateVoucher($vouchers, $attributes);
        return $voucher;
    }

    public function viewVoucherList(): array
    {
        return $this->transforming($this->__resources['view'], function () {
            return $this->prepareViewVoucherlist();
        });
    }

    public function prepareDeleteVoucher(?array $attributes = null): Model
    {
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Id not found');

        $model = $this->voucher(fn($q) => $q->where("status", Status::ACTIVE->value))->find($attributes['id']);
        if (isset($model)) {
            $model->status = Status::INACTIVE->value;
            $model->save();
        }
        return $model;
    }

    public function deleteVoucher(): Model
    {
        return $this->transaction(function () {
            return $this->prepareDeleteVoucher();
        });
    }

    public function voucher(): Builder
    {
        $this->booting();
        return $this->VoucherModel()->with('voucherRules')->withParameters()->orderBy('name', 'asc');
    }
}
