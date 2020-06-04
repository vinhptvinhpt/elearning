<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageCertificate extends Model
{
    //
    protected $table = 'image_certificate';
    protected $fillable = [
        'path', 'is_active', 'description', 'position', 'name', 'organization_id', 'type', 'create_at', 'update_at'
    ];
}
