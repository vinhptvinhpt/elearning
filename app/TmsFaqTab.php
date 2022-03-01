<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsFaqTab extends Model
{
    public function faqs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('\App\TmsFaq', 'tab_id','id');
    }
}
