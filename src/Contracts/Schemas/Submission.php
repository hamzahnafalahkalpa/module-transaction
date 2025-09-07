<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Hanafalah\ModuleTransaction\Contracts\Data\SubmissionData;
//use Hanafalah\ModuleTransaction\Contracts\Data\SubmissionUpdateData;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModuleTransaction\Schemas\Submission
 * @method mixed export(string $type)
 * @method self conditionals(mixed $conditionals)
 * @method array updateSubmission(?SubmissionData $submission_dto = null)
 * @method Model prepareUpdateSubmission(SubmissionData $submission_dto)
 * @method bool deleteSubmission()
 * @method bool prepareDeleteSubmission(? array $attributes = null)
 * @method mixed getSubmission()
 * @method ?Model prepareShowSubmission(?Model $model = null, ?array $attributes = null)
 * @method array showSubmission(?Model $model = null)
 * @method Collection prepareViewSubmissionList()
 * @method array viewSubmissionList()
 * @method LengthAwarePaginator prepareViewSubmissionPaginate(PaginateData $paginate_dto)
 * @method array viewSubmissionPaginate(?PaginateData $paginate_dto = null)
 * @method array storeSubmission(?SubmissionData $submission_dto = null)
 * @method Collection prepareStoreMultipleSubmission(array $datas)
 * @method array storeMultipleSubmission(array $datas)
 */

interface Submission extends DataManagement
{
    public function prepareStoreSubmission(SubmissionData $submission_dto): Model;
}