<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsFaq extends Model
{
    public function tab(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('\App\TmsFaqTab', 'id','tab_id');
    }
}
