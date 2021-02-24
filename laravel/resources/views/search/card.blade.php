<?php $tpl = mb_strtolower(str_replace('App\\', '', $result->searchable_type)); ?>

@include('search.cards.' . $tpl, ['searchable' => $result->searchable])
