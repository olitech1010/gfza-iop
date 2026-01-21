<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            // Auto-generate staff_id if not set
            if (empty($user->staff_id)) {
                $user->staff_id = self::generateStaffId();
            }

            // Auto-generate email from first and last name if not set
            if (empty($user->email) && ! empty($user->first_name) && ! empty($user->last_name)) {
                $user->email = self::generateEmail($user->first_name, $user->last_name);
            }
        });
    }

    /**
     * Generate a unique staff ID in format GFZA/XXX/YY
     */
    public static function generateStaffId(): string
    {
        $currentYear = date('y'); // 2-digit year (e.g., 26 for 2026)

        // Get the highest sequence number for this year
        $lastStaffId = self::query()
            ->where('staff_id', 'like', "GFZA/%/{$currentYear}")
            ->orderByRaw('CAST(SUBSTRING(staff_id, 6, 3) AS UNSIGNED) DESC')
            ->value('staff_id');

        if ($lastStaffId) {
            // Extract the sequence number and increment
            preg_match('/GFZA\/(\d+)\/\d+/', $lastStaffId, $matches);
            $nextSequence = (int) $matches[1] + 1;
        } else {
            $nextSequence = 1;
        }

        return sprintf('GFZA/%03d/%s', $nextSequence, $currentYear);
    }

    /**
     * Generate email from first and last name
     */
    public static function generateEmail(string $firstName, string $lastName): string
    {
        // Sanitize: lowercase, remove special chars, replace spaces with empty string
        $firstName = Str::lower(preg_replace('/[^a-zA-Z]/', '', $firstName));
        $lastName = Str::lower(preg_replace('/[^a-zA-Z]/', '', $lastName));

        $baseEmail = "{$firstName}.{$lastName}@gfza.gov.gh";

        // Check for uniqueness and add number suffix if needed
        $email = $baseEmail;
        $counter = 1;

        while (self::where('email', $email)->exists()) {
            $email = "{$firstName}.{$lastName}{$counter}@gfza.gov.gh";
            $counter++;
        }

        return $email;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'staff_id',
        'department_id',
        'job_title',
        'phone',
        'location_id',
        'is_active',
        'is_nss',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

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
        ];
    }
}
