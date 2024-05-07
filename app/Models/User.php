<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\RolePermission;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp_expire_at',
        'otp',
        'department_id',
        'designation_id',
        'company_id',
        'remember_token',
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expire_at' => 'datetime',
    ];

    public function hasPermission(string $permission)
    {

        // Fetch all roles associated with the user
        $roles = $this->roles()->pluck('name', 'id')->toArray();

        // Fetch permissions associated with these roles within the context of the user's company
        $permissions = RolePermission::join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                        ->join('roles', 'role_permissions.role_id', '=', 'roles.id')
                        ->whereIn('role_permissions.role_id', array_keys($roles)) // Filter by user's roles
                        ->where('role_permissions.company_id', auth()->user()->company_id)
                        ->distinct()
                        ->pluck('permissions.name');
        // dd($permissions);

        // Check if the given permission exists in the derived permissions
        // return $permissions->contains($permission);
        return ['permissions' => $permissions, 'roles' => $roles];


    } 

    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id')
                    ->wherePivot('company_id', auth()->user()->company_id); // Adjust this according to your authentication logic
    }

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    // }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
