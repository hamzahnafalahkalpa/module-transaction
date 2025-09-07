<?php

namespace Hanafalah\ModuleTransaction\Models;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\ModuleTransaction\Resources\Submission\{
    ViewSubmission,
    ShowSubmission
};
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Submission extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;
    
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $list = [
        'id',
        'name',
        'props',
    ];

    protected $casts = [
        'name' => 'string'
    ];


    public function viewUsingRelation(): array{
        return ['transaction'];
    }

    public function showUsingRelation(): array{
        return ['transaction'];
    }

    public function getViewResource(){
        return ViewSubmission::class;
    }

    public function getShowResource(){
        return ShowSubmission::class;
    }
}
