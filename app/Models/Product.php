<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $sku
 * @property string|null $description
 * @property string|null $image
 * @property float $base_price
 * @property float $selling_price
 * @property float $profit_percentage
 * @property bool $is_active
 * @property bool $is_flash_sale
 * @property float|null $flash_sale_price
 * @property \Illuminate\Support\Carbon|null $flash_sale_start
 * @property \Illuminate\Support\Carbon|null $flash_sale_end
 * @property int $sort_order
 * @property string|null $digiflazz_code
 * @property bool $requires_game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read float $current_price
 * @property-read bool $is_flash_sale_active
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDigiflazzCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereFlashSaleEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereFlashSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereFlashSaleStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsFlashSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProfitPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRequiresGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder|Product flashSale()
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'image',
        'base_price',
        'selling_price',
        'profit_percentage',
        'is_active',
        'is_flash_sale',
        'flash_sale_price',
        'flash_sale_start',
        'flash_sale_end',
        'sort_order',
        'digiflazz_code',
        'requires_game_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'is_flash_sale' => 'boolean',
        'flash_sale_price' => 'decimal:2',
        'flash_sale_start' => 'datetime',
        'flash_sale_end' => 'datetime',
        'sort_order' => 'integer',
        'requires_game_id' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the orders for the product.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the current price (flash sale or regular).
     *
     * @return float
     */
    public function getCurrentPriceAttribute(): float
    {
        if ($this->is_flash_sale_active) {
            return $this->flash_sale_price ?? $this->selling_price;
        }
        
        return $this->selling_price;
    }

    /**
     * Check if flash sale is currently active.
     *
     * @return bool
     */
    public function getIsFlashSaleActiveAttribute(): bool
    {
        if (!$this->is_flash_sale) {
            return false;
        }

        $now = now();
        
        return $now->between($this->flash_sale_start, $this->flash_sale_end);
    }

    /**
     * Scope a query to only include active products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include flash sale products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFlashSale($query)
    {
        return $query->where('is_flash_sale', true)
            ->whereNotNull('flash_sale_start')
            ->whereNotNull('flash_sale_end')
            ->where('flash_sale_start', '<=', now())
            ->where('flash_sale_end', '>=', now());
    }
}