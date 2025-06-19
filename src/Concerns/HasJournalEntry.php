<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasJournalEntry
{
    use HasTransaction;

    public function isHasJournalEntry(){
        return true;
    }

    public static function bootHasJournalEntry()
    {
        static::creating(function ($query) {
            $query->journal_reported_at = null;
        });
    }

    public function isReported():bool{
        return $this->isDirty('reported_at') && isset($this->reported_at);
    }

    public function journalEntry(){return $this->morphOneModel('JournalEntry','reference');}
}
