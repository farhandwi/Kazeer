<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Foods extends Model
{
    use HasFactory;
    use Search;

    protected $guarded = [];

    protected $fillable = [
        'name', 'description', 'image', 'is_active', 'is_available', 'price', 
        'price_afterdiscount', 'percent', 'categories_id', 'promo_start_at', 'promo_end_at'
    ];

    protected $searchable = ['name', 'description'];

    protected $casts = [
        'promo_start_at' => 'datetime',
        'promo_end_at' => 'datetime',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function getAllFoods()
    {
        return DB::table('foods')
            ->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
            ->select('foods.*', DB::raw('COALESCE(SUM(transaction_items.quantity), 0) as total_sold'))
            ->groupBy('foods.id')
            ->get()
            ->map(function ($item) {
                $item->is_promo = $item->promo_start_at && 
                    $item->promo_end_at && 
                    now()->between($item->promo_start_at, $item->promo_end_at);
                return $item;
            });
    }

    public function getFoodDetails($id)
    {
        return DB::table('foods')
            ->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
            ->select('foods.*', DB::raw('COALESCE(SUM(transaction_items.quantity), 0) as total_sold'))
            ->where('foods.id', $id)
            ->groupBy('foods.id')
            ->get()
            ->map(function ($item) {
                $item->is_promo = $item->promo_start_at && 
                    $item->promo_end_at && 
                    now()->between($item->promo_start_at, $item->promo_end_at);
                return $item;
            });
    }

    public function getPromo()
    {
        $now = now();
        return DB::table('foods')
            ->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
            ->select('foods.*', DB::raw('COALESCE(SUM(transaction_items.quantity), 0) as total_sold'))
            ->where('foods.promo_start_at', '<=', $now)
            ->where('foods.promo_end_at', '>=', $now)
            ->groupBy('foods.id')
            ->get()
            ->map(function ($item) {
                $item->is_promo = $item->promo_start_at && 
                    $item->promo_end_at && 
                    now()->between($item->promo_start_at, $item->promo_end_at);
                return $item;
            });
    }

    public function getFavoriteFood()
    {
        return TransactionItems::select(
            'foods.*',
            DB::raw('SUM(transaction_items.quantity) as total_sold')
        )
        ->join('foods', 'transaction_items.foods_id', '=', 'foods.id')
        ->groupBy('foods.id')
        ->orderByDesc('total_sold')
        ->get()
        ->map(function ($item) {
            $item->is_promo = $item->promo_start_at && 
                $item->promo_end_at && 
                now()->between($item->promo_start_at, $item->promo_end_at);
            return $item;
        });
    }

    public function getIsCurrentlyPromoAttribute()
    {
        return $this->promo_start_at && 
            $this->promo_end_at && 
            now()->between($this->promo_start_at, $this->promo_end_at);
    }

    public function getPromoStatusAttribute()
    {
        if (!$this->promo_start_at || !$this->promo_end_at) {
            return 'No Promo';
        }

        $now = now();
        if ($this->promo_start_at <= $now && $this->promo_end_at >= $now) {
            return 'Active';
        }

        if ($this->promo_start_at > $now) {
            return 'Upcoming';
        }

        return 'Expired';
    }

    public function getIsPromoAttribute()
    {
        if (!$this->promo_start_at || !$this->promo_end_at) {
            return false;
        }

        $now = now();
        return $this->promo_start_at <= $now && $this->promo_end_at >= $now;
    }

    public static function getProductCountByCategory()
    {
        return DB::table('categories')
            ->leftJoin('foods', 'categories.id', '=', 'foods.categories_id')
            ->select('categories.id', 'categories.name', DB::raw('COUNT(foods.id) as product_count'))
            ->groupBy('categories.id', 'categories.name')
            ->get();
    }
}