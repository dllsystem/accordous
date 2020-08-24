<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'address_zipcode',
        'address_street',
        'address_number',
        'address_complement',
        'address_neighborhood',
        'address_city',
        'address_state',
        'owner_name',
        'owner_email',
        'is_rent'
    ];

    protected $hidden = ["created_at", "updated_at"];

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}
