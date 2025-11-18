<?php

namespace Hanafalah\ModuleTransaction\Models;

use Hanafalah\LaravelSupport\Models\Unicode\Unicode;
use Hanafalah\ModuleTransaction\Resources\MasterReport\{
    ViewMasterReport,
    ShowMasterReport
};

class MasterReport extends Unicode
{
    protected $table = 'unicodes';

    public function getViewResource(){
        return ViewMasterReport::class;
    }

    public function getShowResource(){
        return ShowMasterReport::class;
    }
}
