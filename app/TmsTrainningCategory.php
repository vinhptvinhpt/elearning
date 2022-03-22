<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static select(string $string, string $string1)
 */
class TmsTrainningCategory extends Model
{
    protected $table = 'tms_trainning_categories';
    protected $fillable = [
        'trainning_id', 'category_id'
    ];

}
