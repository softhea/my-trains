<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable //implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'apple_id',
        'avatar',
        'phone',
        'city',
        'role_id',
        'is_protected',
        'email_verified_at'
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * Get the user's profile image.
     */
    public function image()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable');
    }

    /**
     * Get all images for this user.
     */
    public function images()
    {
        return $this->morphMany(\App\Models\Image::class, 'imageable');
    }

    /**
     * Get the user's profile image URL or return null.
     */
    public function getImageUrlAttribute()
    {
        // Prioritize Google avatar, then uploaded image
        if ($this->avatar) {
            return $this->avatar;
        }
        
        return $this->image ? $this->image->url : null;
    }

    /**
     * Get the role that belongs to the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    /**
     * Check if user is superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if user is admin (includes superadmin).
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'superadmin']);
    }

    /**
     * Check if user can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_protected && !$this->isSuperAdmin();
    }

    /**
     * Get the role display name.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->display_name : 'User';
    }

    /**
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Products created/owned by the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread messages count.
     */
    public function unreadMessagesCount(): int
    {
        return $this->receivedMessages()->unread()->count();
    }

    /**
     * Get latest unread messages.
     */
    public function latestUnreadMessages($limit = 5)
    {
        return $this->receivedMessages()
            ->with(['sender', 'product'])
            ->unread()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get the authentication provider for this user.
     */
    public function getAuthProviderAttribute(): string
    {
        if ($this->google_id) {
            return 'Google';
        }
        
        if ($this->apple_id) {
            return 'Apple';
        }
        
        return 'Email';
    }

    /**
     * Check if user signed up via Google.
     */
    public function isGoogleUser(): bool
    {
        return !empty($this->google_id);
    }

    /**
     * Check if user signed up via Apple.
     */
    public function isAppleUser(): bool
    {
        return !empty($this->apple_id);
    }

    /**
     * Check if user signed up via social providers.
     */
    public function isSocialUser(): bool
    {
        return $this->isGoogleUser() || $this->isAppleUser();
    }

    /**
     * Check if user is verified (has email_verified_at set).
     */
    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if user can add products (must be verified).
     */
    public function canAddProducts(): bool
    {
        return $this->isVerified();
    }

    /**
     * Check if user can place orders (must be verified).
     */
    public function canPlaceOrders(): bool
    {
        return $this->isVerified();
    }
}
