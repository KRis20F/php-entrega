<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        logger()->info('Checking role', ['user_id' => $this->id, 'role_id' => $this->role_id, 'required_role' => $roleName]);

        // Obtener el rol directamente de la base de datos
        $role = Role::find($this->role_id);

        // Verificar si el usuario tiene un rol asignado
        if (!$role) {
            logger()->warning('User has no role', ['user_id' => $this->id]);
            return false;
        }

        // Verificar si el rol coincide
        $hasRole = $role->name === $roleName;
        logger()->info('Role check result', [
            'user_id' => $this->id,
            'user_role' => $role->name,
            'required_role' => $roleName,
            'has_role' => $hasRole
        ]);

        return $hasRole;
    }
}
