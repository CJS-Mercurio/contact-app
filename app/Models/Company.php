<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    // protected $fillable = ['name', 'email', 'address', 'website'];
    // protected $table = "app_companies";
    // protected $table = "_id";

    public function contacts() {

        return $this->hasMany(Contact::class);
    }
}
