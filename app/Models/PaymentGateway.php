<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentGateway
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property array $config
 * @property bool $is_active
 * @property bool $is_sandbox
 * @property float $fee_percentage
 * @property float $fee_fixed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereFeeFixed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereFeePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereIsSandbox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway active()

 * 
 * @mixin \Eloquent
 */
class PaymentGateway extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'config',
        'is_active',
        'is_sandbox',
        'fee_percentage',
        'fee_fixed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
    ];

    /**
     * Scope a query to only include active payment gateways.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}