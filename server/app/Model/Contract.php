<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{

    protected $fillable = [
        'tenant_document_type',
        'tenant_document',
        'tenant_name',
        'tenant_email',
        'property_id',
    ];

    protected $hidden = ["created_at", "updated_at"];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
