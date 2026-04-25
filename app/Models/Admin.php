<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'admin_id',
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'image',
        'aadhaar_number',
        'pan_number',
        'driving_license',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =====================================
       AUTO GENERATE ADMIN ID (SAFE)
    ===================================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {

            $nextId = self::max('id') + 1;

            $admin->admin_id = 'ADM' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            if (empty($admin->status)) {
                $admin->status = 'pending';
            }
        });
    }

    /* =====================================
       RELATIONSHIP
    ===================================== */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
//     public function hasPermission($permission)
// {
//     return true; // TEMP: allow everything
// }
public function hasPermission(string $permissionName): bool
{
    // Super Admin always allowed
    if ($this->isSuperAdmin()) {
        return true;
    }

    if (!$this->role) {
        return false;
    }

    return $this->role
        ->permissions()
        ->where('name', $permissionName)
        ->exists();
}

    /* =====================================
       IMAGE ACCESSOR
    ===================================== */

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path('uploads/admins/' . $this->image))) {
            return asset('uploads/admins/' . $this->image);
        }

        return asset('default-avatar.png');
    }

    /* =====================================
       STATUS HELPERS
    ===================================== */

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
public function isSuperAdmin(): bool
{
    return $this->role_id == 1;
}
protected $appends = ['image_url'];

}
