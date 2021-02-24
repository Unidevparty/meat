<?php

namespace App;
use Carbon\Carbon;

trait SearchIndex
{
    public function search()
    {
        return $this->morphOne(Search::class, 'searchable');
    }

    public function getIspublishedAttribute()
    {
        if (!$this->published) return false;

        if ($this->published_at > Carbon::now()) return false;

        return true;
    }

    public function updateSearchIndex()
    {
        $searchindex = '';

        foreach ($this->searchattributes as $attribute) {
            $searchindex .= ' ' . strip_tags($this->$attribute);
        }

        // оптимизация поискового индекса
        // $searchindex = str_replace([',', ':', ';', '.', '(', ')', '!', '&', '%'], ' ', $searchindex);
        // $searchindex = preg_replace("/\s+/", ' ', $searchindex);
        // $searchindex = mb_strtolower($searchindex);
        // $searchindex = explode(' ', $searchindex);

        // $searchindex = array_filter($searchindex, function($element) {
        //     return mb_strlen($element) >= 4;
        // });
        //
        // $searchindex = array_unique($searchindex);
        // $searchindex = implode(' ', $searchindex);

        if (!$this->search) {
            $search = new Search;

            $search->searchindex    = $searchindex;
            $search->published_at   = $this->published_at;
            $search->name           = $this->name;
            $search->views          = $this->views;

            $this->search()->save($search);
        } else {
            $this->search->searchindex    = $searchindex;
            $this->search->published_at   = $this->published_at;
            $this->search->name           = $this->name;
            $this->search->views          = $this->views;

            $this->search->save();
        }
    }

    public function save(array $data = [])
    {
        parent::save($data);

        if (!$this->ispublished) {

            if ($this->search) {
                $this->search->delete();
            }

            return;
        }

        $this->updateSearchIndex();
    }

    public function delete()
    {
        if ($this->search) {
            $this->search->delete();
        }

        parent::delete();
    }
}
