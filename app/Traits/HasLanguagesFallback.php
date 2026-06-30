<?php

namespace App\Traits;

trait HasLanguagesFallback
{
    /**
     * Get the languages relationship with sorting and safety fallbacks.
     */
    public function getLanguagesAttribute()
    {
        if (! $this->relationLoaded('languages')) {
            $this->load('languages');
        }

        $collection = $this->getRelation('languages');

        $activeLanguageId = config('app.language_id', 1);

        $sorted = $collection->sortBy(function($lang) use ($activeLanguageId) {
            return $lang->id == $activeLanguageId ? 0 : 1;
        });

        if ($sorted->isEmpty()) {
            $dummyPivot = new class {
                public function __get($name) {
                    return '';
                }
            };
            $dummyLang = new class($dummyPivot) {
                public $pivot;
                public $id = 0;
                public function __construct($pivot) {
                    $this->pivot = $pivot;
                }
                public function __get($name) {
                    return '';
                }
            };
            return collect([$dummyLang]);
        }

        return $sorted;
    }
}
