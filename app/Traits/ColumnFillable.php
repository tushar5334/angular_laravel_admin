<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait ColumnFillable
{
    public function getFillable()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        // return Schema::getColumnListing($this->getTable());
    }
}
