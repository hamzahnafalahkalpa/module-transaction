<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\ModuleItem\Contracts\CardStock;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Consument as ContractsConsument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModuleTransaction\Resources\Consument\ShowConsument;
use Hanafalah\ModuleTransaction\Resources\Consument\ViewConsument;

class Consument extends PackageManagement implements ContractsConsument
{
    protected array $__guard      = [];
    protected array $__add        = [];
    protected string $__entity    = 'Consument';
    public static $consument_model;

    protected array $__resources = [
        'view' => ViewConsument::class,
        'show' => ShowConsument::class
    ];

    protected array $__cache = [
        'show' => [
            'name'     => 'consument',
            'tags'     => ['consument', 'consument-show'],
            'duration' => 60
        ]
    ];

    public function prepareStoreConsument(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $consument = $this->consument()->updateOrCreate([], []);
        return static::$consument_model = $consument;
    }

    public function showUsingRelation(): array
    {
        return [
            'reference'
        ];
    }

    public function prepareShowConsument(?Model $model = null, ?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $model ??= $this->getConsument();
        if (!isset($model)) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Consument not found.', 422);

            $model = $this->consument()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return static::$consument_model = $model;
    }

    public function showConsument(?Model $model = null)
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowConsument($model);
        });
    }

    public function prepareViewConsumentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator
    {
        $attributes ??= request()->all();

        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');

        $consument_model = $this->consument()->orderBy('created_at', 'desc')
            ->paginate(...$this->arrayValues($paginate_options))
            ->appends(request()->all());

        return static::$consument_model = $consument_model;
    }

    public function viewConsumentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['view'], function () use ($paginate_options) {
            return $this->prepareViewConsumentPaginate(...$this->arrayValues($paginate_options));
        });
    }

    public function prepareDeleteConsument(?array $attributes = null): bool
    {
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Consument not found.', 422);

        $consument = $this->ConsumentModel()->findOrFail($attributes['id']);
        return $consument->delete();
    }

    public function deleteConsument(): bool
    {
        return $this->transaction(function () {
            return $this->prepareDeleteConsument();
        });
    }

    public function consument(mixed $conditionals = null): Builder
    {
        $this->booting();
        return $this->ConsumentModel()->conditionals($conditionals)
            ->when(isset(request()->consument_id), function ($query) {
                $query->where('consument_id', request()->consument_id);
            })->when(isset(request()->consument_type), function ($query) {
                $query->where('consument_type', request()->consument_type);
            })->withParameters();
    }

    public function getConsument(): mixed
    {
        return static::$consument_model;
    }
}
