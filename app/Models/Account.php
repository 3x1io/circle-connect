<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Jetstream\HasTeams;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use TomatoPHP\FilamentSaasPanel\Traits\InteractsWithTenant;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $loginBy
 * @property string $type
 * @property string $address
 * @property string $password
 * @property string $otp_code
 * @property string $otp_activated_at
 * @property string $last_login
 * @property string $agent
 * @property string $host
 * @property int $attempts
 * @property bool $login
 * @property bool $activated
 * @property bool $blocked
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class Account extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasTenants
{
    use HasFactory;
    use HasTeams;
    use InteractsWithMedia;
    use InteractsWithTenant;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'phone',
        'parent_id',
        'type',
        'name',
        'username',
        'loginBy',
        'address',
        'password',
        'otp_code',
        'otp_activated_at',
        'last_login',
        'agent',
        'host',
        'is_login',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_login' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'otp_activated_at',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'otp_activated_at',
        'host',
        'agent',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?? null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function accountMeta(): HasMany
    {
        return $this->hasMany(AccountMeta::class);
    }

    /**
     * @return string[]
     */
    public function dataFields(): array
    {
        return [
            'address.company',
            'address.salutation',
            'address.title',
            'address.first_name',
            'address.last_name',
            'address.street',
            'address.number',
            'address.postcode',
            'address.city',
            'address.country',
            'address.letter_salutation',
            'payments.payment_type',
            'payments.id_number',
            'payments.direct_debit',
            'payments.bank_name',
            'payments.account_owner',
            'payments.iban',
            'payments.bic',
            'payments.card_type',
            'payments.credit_card_owner',
            'payments.credit_card_number',
            'payments.credit_card_expiry',
            'payments.credit_card_cvc',
            'communication.tel_direct',
            'communication.tel_mobile',
            'communication.tel_secretary',
            'communication.fax_number',
            'communication.email',
            'additional.important',
            'additional.name_of_secretary',
            'additional.date_of_birth',
            'additional.evaluation',
            'status.price_per_bottle_max',
            'status.items',
            'cancellation.canceling_reasons',
        ];
    }

    public function checkMeta(): void
    {
        foreach ($this->dataFields() as $field) {
            $type = explode('.', $field)[0];
            $key = explode('.', $field)[1];
            if (! $this->accountMeta()->where('type', $type)->where('key', $key)->exists()) {
                $this->accountMeta()->create([
                    'type' => $type,
                    'key' => $key,
                    'user_id' => auth('accounts')->user() ? User::first()->id : auth()->user()->id,
                ]);
            }
        }
    }

    public function loadData(): Collection
    {
        return $this->accountMeta()->whereIn('type', collect($this->dataFields())->map(fn ($item) => str($item)->explode('.')[0])->toArray())->get();
    }

    public function meta(
        string $key
    ): mixed {
        return $this->accountMeta()->where('key', $key)->first()?->key_value;
    }

    public function metaArray(
        string $key
    ): mixed {
        return $this->accountMeta()->where('key', $key)->first()?->value;
    }

    public function team()
    {
        return $this->teams();
    }
}
