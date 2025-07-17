<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasJournalEntry
{
    use HasTransaction;

    public function isHasJournalEntry(){
        return true;
    }

    public function journalEntry(){return $this->morphOneModel('JournalEntry','reference');}
}
