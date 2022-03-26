<?php

namespace App\Models\Member;

use App\Models\User;
use Carbon\Carbon;
use Database\Factories\Member\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ContactFactory::new();
    }

    protected $table = "customers";

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "type",
        "gender",
        "dob",
        "phone",
        "email",
        "address",
        "city",
        "state",
        "pincode",
        "created_by",
    ];

    /**
     * Mutator for setting the Date of birth date in Y-m-d format
     * @param $value
     */
    public function setDobAttribute($value)
    {
        $this->attributes["dob"] = Carbon::parse($value)->format("Y-m-d");
    }

    /**
     * Accessor for returning the date of birth in m/d/Y format
     * @param $value
     * @return string
     */
    public function getDobAttribute($value)
    {
        return Carbon::parse($value)->format("m/d/Y");
    }

    /**
     * Accessor for returning the user name instead of the ID for created_by attribute
     * @param $value
     * @return string
     */
    public function getCreatedByAttribute($value)
    {
        return ucwords(User::find($value)->name);
    }

    /**
     * Accessor for getting the name
     * @param $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function scopeCustomer($query)
    {
        return $query->where("type", "customer");
    }

    public function scopeProspect($query)
    {
        return $query->where("type", "prospect");
    }

    /**
     * Get the account associated with the contact.
     */
    public function account()
    {
        return $this->hasOne(Account::class, "contact_id");
    }
}
