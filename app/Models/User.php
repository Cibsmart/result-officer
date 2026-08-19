<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;

final class User extends Authenticatable implements FilamentUser
{
    use Notifiable;
    use SoftDeletes;

    /** @var list<string> */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ImportEvent, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ImportEvent, $this>
     */
    public function imports(): HasMany
    {
        return $this->hasMany(ImportEvent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ExcelImportEvent, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ExcelImportEvent, $this>
     */
    public function excelImportEvents(): HasMany
    {
        return $this->hasMany(ExcelImportEvent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserDepartment, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserDepartment, $this>
     */
    public function departments(): HasMany
    {
        return $this->hasMany(UserDepartment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingEventGroup, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingEventGroup, $this>
     */
    public function vettingEventGroups(): HasMany
    {
        return $this->hasMany(VettingEventGroup::class);
    }

    public function inDomain(): bool
    {
        return str_ends_with($this->email, Config::string('rp.domain'));
    }

    /**
     * Department ids this user may act on. Empty for an unassigned user, which
     * denies everything scoped — administrators bypass the check entirely.
     *
     * @return list<int>
     */
    public function accessibleDepartmentIds(): array
    {
        return array_values(array_map(
            static fn (mixed $id): int => (int) $id,
            $this->departments()->pluck('department_id')->all(),
        ));
    }

    public function canAccessDepartment(int $departmentId): bool
    {
        return $this->isAdmin() || in_array($departmentId, $this->accessibleDepartmentIds(), true);
    }

    public function isAdmin(): bool
    {
        return $this->inDomain() && in_array($this->role, [Role::SUPER_ADMIN, Role::ADMIN], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->inDomain() && $this->role === Role::SUPER_ADMIN;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        return true;
    }

    public function canViewHorizon(): bool
    {
        return $this->isAdmin();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtoupper($value),
        );
    }
}
