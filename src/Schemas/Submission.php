<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\{
    Supports\BaseModuleTransaction
};
use Hanafalah\ModuleTransaction\Contracts\Schemas\Submission as ContractsSubmission;
use Hanafalah\ModuleTransaction\Contracts\Data\SubmissionData;

class Submission extends BaseModuleTransaction implements ContractsSubmission
{
    protected string $__entity = 'Submission';
    public $submission_model;
    //protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'submission',
            'tags'     => ['submission', 'submission-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStore(SubmissionData $submission_dto): Model{
        return $this->prepareStoreSubmission($submission_dto);
    }

    public function prepareStoreSubmission(SubmissionData $submission_dto): Model{
        $add = [
            'name' => $submission_dto->name
        ];
        $guard  = ['id' => $submission_dto->id];
        $create = [$guard, $add];
        // if (isset($submission_dto->id)){
        //     $guard  = ['id' => $submission_dto->id];
        //     $create = [$guard, $add];
        // }else{
        //     $create = [$add];
        // }

        $submission = $this->usingEntity()->updateOrCreate(...$create);
        $this->fillingProps($submission,$submission_dto->props);
        $submission->save();
        return $this->submission_model = $submission;
    }
}