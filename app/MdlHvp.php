<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MdlHvp extends Model
{
    protected $table = 'mdl_hvp';
    protected $fillable = [
        'course', 'name', 'intro', 'introformat', 'json_content', 'embed_type',
        'main_library_id', 'content_type', 'authors', 'source', 'year_from',
        'year_to', 'license', 'license_version', 'changes', 'license_extras', 'author_comments',
        'default_language', 'filtered', 'slug', 'timecreated', 'timemodified'
    ];
}
