<?php

namespace App\Entities\Account;

use App\Entities\CBT\Exam;
use App\Notifications\User\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use App\Entities\CBT\Participant;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Extra\Eloquent\Concerns\Attachable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property Participant|null detail
 * @property int id
 * @property string name
 * @property string email
 * @property string username
 * @property \Illuminate\Database\Eloquent\Collection $roles
 * @property mixed|string $password
 * @property mixed $alt_id
 * @method static Builder whereIs(string $role)
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRolesAndAbilities, HashableId, Attachable, SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'alt_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'hash',
    ];

    /**
     * Define relation to exam that user participate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function exams(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'participants', 'user_id', 'exam_id')
            ->withPivot(['id', 'status'])
            ->using(Participant::class)
            ->as('detail');
    }

    /**
     * set mutation for password to be bcrypt.
     *
     * @param $value
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Find for passport.
     *
     * @param $username
     *
     * @return mixed
     */
    public function findForPassport($username)
    {
        return self::query()->where('email', $username)->orWhere('username', $username)->first();
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'notification.'.$this->getAttribute('username');
    }

    public function canParticipate(string $examId): bool
    {
        return $this->exams()->wherePivot('exam_id', $examId)->count() > 0;
    }

    public function scopeSearch(Builder $builder, $keyword)
    {
        $builder->where(fn (Builder $builder) => collect(Arr::except($this->fillable, $this->hidden))->each(
            fn ($column) => $builder->orWhere($column, 'like', "%{$keyword}%")));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
