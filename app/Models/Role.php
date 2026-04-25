<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

public function permissions()
{
    return $this->belongsToMany(
        Permission::class,
        'permission_role',
        'role_id',
        'permission_id'
    );
}

}
