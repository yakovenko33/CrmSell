<?php

namespace CrmSell\Users\Domains\Entities;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use CrmSell\Common\Domains\Traits\UuidTrait;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, UuidTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        "first_name",
        "last_name",
        'created_by',
        'modified_user_id',
        "created_at",
        "updated_at"
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
        'password' => 'hashed',
    ];

    public function getForInit(): array {
        return [
            "id" => $this->id,
            "email" => $this->email,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
        ];
    }

    public function getDetail(): array {
        return [
            "id" => $this->id,
            "email" => $this->email,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "status" => $this->status,
        ];
    }

    public function isNotActive(): bool {
        return $this->status === 1;
    }
}
