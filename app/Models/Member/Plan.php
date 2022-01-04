<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * Get the contact associated with the account.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
