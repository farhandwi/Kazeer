<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Foods;
use App\Models\Category;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.page')]
#[Title('MyFOOD - Home')]
class HomePage extends Component
{
    // Search and Filter Properties
    public $term = '';
    public $selectedFilter = 'all';
    public $showGridView = true; 
    
    // New filter properties
    public $selectedPriceFilters = [];
    public $selectedCategoryFilters = [];
    
    // Data Properties
    public $promos;
    public $favorites;
    public $foodsByCategory;
    public $categories;
    public $searchResult;
    
    // User Properties
    public $isCustomerDataComplete = false;
    public $tableNumber;
    
    // UI State Properties
    public $isLoading = false;
    public $filterModalOpen = false;

    protected $listeners = [
        'saved-user-info' => 'handleUserInfoSaved',
        'show-filter-modal' => 'showFilterModal',
        'close-filter-modal' => 'closeFilterModal'
    ];

    public function mount()
    {
        $this->initializeData();
        $this->checkCustomerData();
    }

    /**
     * Initialize all required data
     */
    private function initializeData()
    {
        try {
            // Load categories
            $this->categories = Category::orderBy('name')
                ->get();

            $foods = new Foods();
            
            // Load promo foods (with discount > 0)
            $this->promos = $foods->getPromo()
            ->where('is_active', true)
            ->take(6);        

            // Load favorite foods
            $this->favorites = $foods->getFavoriteFood()
            ->where('is_active', true)
            ->take(8);

            // Load foods by category
            $this->foodsByCategory = $this->getFoodsByCategory();
            Log::info('Foods by category: ' . json_encode($this->foodsByCategory));
            
            // Initialize search result as empty collection
            $this->searchResult = collect();
            
            // Get table number from session
            $this->tableNumber = session('table_number', 'No Table');
            
        } catch (\Exception $e) {
            // Handle error gracefully
            $this->promos = collect();
            $this->favorites = collect();
            $this->foodsByCategory = collect();
            $this->categories = collect();
            Log::error($e->getMessage());
            
            session()->flash('error', 'Terjadi kesalahan saat memuat data makanan.');
        }
    }

    /**
     * Get foods organized by category
     */
    private function getFoodsByCategory()
    {
        try {
            // Hitung batas harga untuk 'termurah' dan 'termahal' jika diperlukan
            $lowestPrice = null;
            $highestPrice = null;
    
            if (!empty($this->selectedPriceFilters)) {
                $allPrices = Foods::where('is_active', true)
                    ->orderBy('price')
                    ->pluck('price')
                    ->values();
    
                $count = $allPrices->count();
    
                if ($count > 0) {
                    $lowestIndex = floor($count * 0.25);
                    $highestIndex = floor($count * 0.75);
    
                    $lowestPrice = $allPrices[$lowestIndex] ?? null;
                    $highestPrice = $allPrices[$highestIndex] ?? null;
                }
            }
    
            $query = Category::with(['foods' => function ($foodQuery) use ($lowestPrice, $highestPrice) {
                $foodQuery->where('is_active', true);
    
                if (!empty($this->selectedPriceFilters)) {
                    $foodQuery->where(function ($priceQuery) use ($lowestPrice, $highestPrice) {
                        foreach ($this->selectedPriceFilters as $priceFilter) {
                            switch ($priceFilter) {
                                case 'termurah':
                                    if ($lowestPrice !== null) {
                                        $priceQuery->orWhere('price', '<=', $lowestPrice);
                                    }
                                    break;
                                case 'termahal':
                                    if ($highestPrice !== null) {
                                        $priceQuery->orWhere('price', '>=', $highestPrice);
                                    }
                                    break;
                                case 'diskon':
                                    $priceQuery->orWhere('discount_percentage', '>', 0);
                                    break;
                                case 'terfavorit':
                                    $priceQuery->orWhere('is_favorite', true);
                                    break;
                            }
                        }
                    });
                }
    
                $foodQuery->orderBy('name')->take(6);
            }]);
    
            if (!empty($this->selectedCategoryFilters)) {
                $query->whereIn('id', $this->selectedCategoryFilters);
            }
    
            return $query->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug ?? strtolower(str_replace(' ', '-', $category->name)),
                        'foods' => $category->foods,
                        'foods_count' => $category->foods->count(),
                    ];
                })
                ->filter(fn ($category) => $category['foods_count'] > 0);
        } catch (\Exception $e) {
            Log::error('getFoodsByCategory error: ' . $e->getMessage());
            return collect();
        }
    }
    
    

    /**
     * Check customer data completion
     */
    private function checkCustomerData()
    {
        $name = session('name');
        $phone = session('phone');
        $tableNumber = session('table_number');
        
        // Customer data is incomplete if any required field is missing
        $this->isCustomerDataComplete = empty($name) || empty($phone) || empty($tableNumber);
    }

    /**
     * Handle real-time search
     */
    public function updatedTerm()
    {
        $this->isLoading = true;
    
        try {
            if (!empty(trim($this->term))) {
                $query = Foods::query()
                    ->search(trim($this->term))
                    ->where('is_active', true)
                    ->with('category'); // Gunakan relasi 'category' bukan 'categories_id'
    
                // Apply filters before get
                $this->applyFiltersToQuery($query);
    
                $this->searchResult = $query->get();
            } else {
                $this->searchResult = collect();
            }
        } catch (\Exception $e) {
            $this->searchResult = collect();
            session()->flash('search_error', 'Terjadi kesalahan saat mencari makanan.');
            Log::error('Search error: ' . $e->getMessage());
        }
    
        $this->isLoading = false;
    }    

    private function applyFiltersToQuery(\Illuminate\Database\Eloquent\Builder $query)
    {
        $total = DB::table('foods')->where('is_active', 1)->count();
        $offset50 = (int) floor($total * 0.50);
    
        // Ambil nilai price di posisi offset 25% paling murah dan paling mahal
        $lowest25Price = DB::table('foods')
            ->where('is_active', 1)
            ->orderBy('price', 'asc')
            ->skip($offset50)
            ->value('price');
    
        $highest25Price = DB::table('foods')
            ->where('is_active', 1)
            ->orderBy('price', 'desc')
            ->skip($offset50)
            ->value('price');
        
            Log::info('lowest25Price: ' . $lowest25Price);
            Log::info('highest25Price: ' . $highest25Price);
    
        // Filter berdasarkan price, diskon, favorit
        if (!empty($this->selectedPriceFilters)) {
            foreach ($this->selectedPriceFilters as $filter) {
                switch ($filter) {
                    case 'termurah':
                        if ($lowest25Price !== null) {
                            $query->where('price', '<=', $lowest25Price);
                        }
                        break;
    
                    case 'termahal':
                        if ($highest25Price !== null) {
                            $query->where('price', '>=', $highest25Price);
                        }
                        break;
    
                    case 'diskon':
                        $query->whereNotNull('percent');
                        break;
    
                    case 'terfavorit':
                        $query->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
                              ->select('foods.*', DB::raw('COUNT(transaction_items.id) as transaction_count'))
                              ->groupBy('foods.id')
                              ->orderByDesc('transaction_count');
                        break;
                }
            }
        }
    
        // Filter kategori
        if (!empty($this->selectedCategoryFilters)) {
            $query->whereIn('foods.categories_id', $this->selectedCategoryFilters);
        }
    }
    
     

    /**
     * Toggle price filter
     */
    public function togglePriceFilter($filter)
    {
        if (in_array($filter, $this->selectedPriceFilters)) {
            $this->selectedPriceFilters = array_diff($this->selectedPriceFilters, [$filter]);
        } else {
            $this->selectedPriceFilters[] = $filter;
        }
        
        $this->refreshFilteredData();
    }

    /**
     * Toggle category filter
     */
    public function toggleCategoryFilter($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategoryFilters)) {
            $this->selectedCategoryFilters = array_diff($this->selectedCategoryFilters, [$categoryId]);
        } else {
            $this->selectedCategoryFilters[] = $categoryId;
        }
        
        $this->refreshFilteredData();
    }

    /**
     * Apply filters
     */
    public function applyFilters()
    {
        $this->refreshFilteredData();
        $this->closeFilterModal();
        
        // If searching, refresh search results
        if (!empty(trim($this->term))) {
            $this->updatedTerm();
        }
        
        session()->flash('success', 'Filter berhasil diterapkan!');
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->selectedPriceFilters = [];
        $this->selectedCategoryFilters = [];
        $this->selectedFilter = 'all';
        
        $this->refreshFilteredData();
        
        session()->flash('success', 'Filter berhasil dihapus!');
    }

    /**
     * Toggle between grid and list view
     */
    public function toggleGridView()
    {
        $this->showGridView = !$this->showGridView;
        
        // Dispatch event untuk update child components
        $this->dispatch('view-mode-changed', [
            'showGridView' => $this->showGridView
        ]);
    }

    /**
     * Set filter for food categories
     */
    public function setFilter($filter)
    {
        $this->selectedFilter = $filter;
        $this->refreshFilteredData();
    }

    /**
     * Refresh data based on selected filter
     */
    private function refreshFilteredData()
    {
        if ($this->selectedFilter === 'all') {
            $this->foodsByCategory = $this->getFoodsByCategory();
        } else {
            // Filter by specific category
            $this->foodsByCategory = $this->getFoodsByCategory()
                ->filter(function ($category) {
                    return $category['id'] == $this->selectedFilter;
                });
        }
        
        // Refresh favorites and promos with filters
        $this->refreshFavoritesAndPromos();
    }

    /**
     * Refresh favorites and promos with current filters
     */
    private function refreshFavoritesAndPromos()
    {
        try {
            // Refresh promos
            $promoQuery = Foods::query()->getPromo()->where('is_active', true);
            $this->applyFiltersToQuery($promoQuery);
            $this->promos = $promoQuery->take(6)->get();
    
            // Refresh favorites
            $favoriteQuery = Foods::query()->getFavoriteFood()->where('is_active', true);
            $this->applyFiltersToQuery($favoriteQuery);
            $this->favorites = $favoriteQuery->take(8)->get();
    
        } catch (\Exception $e) {
            Log::error('Error refreshing favorites and promos: ' . $e->getMessage());
            $this->promos = collect();
            $this->favorites = collect();
        }
    }
    
    

    /**
     * Show filter modal
     */
    public function showFilterModal()
    {
        $this->filterModalOpen = true;
        $this->dispatch('filter-modal-opened');
    }

    /**
     * Close filter modal
     */
    public function closeFilterModal()
    {
        $this->filterModalOpen = false;
        $this->dispatch('filter-modal-closed');
    }

    /**
     * Handle when user info is saved
     */
    public function handleUserInfoSaved()
    {
        $this->isCustomerDataComplete = false;
        session()->flash('success', 'Data berhasil disimpan!');
        
        // Update table number
        $this->tableNumber = session('table_number', 'No Table');
    }

    /**
     * Refresh all data
     */
    public function refreshData()
    {
        $this->initializeData();
        session()->flash('success', 'Data berhasil diperbarui!');
    }

    /**
     * Add to favorites (if needed)
     */
    public function toggleFavorite($foodId)
    {
        try {
            $food = Foods::findOrFail($foodId);
            $food->is_favorite = !$food->is_favorite;
            $food->save();
            
            // Refresh favorites data
            $foods = new Foods();
            $this->favorites = $foods->getFavoriteFood()
                ->where('is_active', true)
                ->take(8)
                ->get();
                
            session()->flash('success', 'Favorit berhasil diupdate!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengupdate favorit.');
        }
    }

    /**
     * Get computed properties for better performance
     */
    public function getHasPromosProperty()
    {
        return $this->promos && $this->promos->count() > 0;
    }

    public function getHasFavoritesProperty()
    {
        return $this->favorites && $this->favorites->count() > 0;
    }

    public function getHasFoodsByCategoryProperty()
    {
        return $this->foodsByCategory && $this->foodsByCategory->count() > 0;
    }

    public function getIsSearchingProperty()
    {
        return !empty(trim($this->term));
    }

    public function getSearchResultCountProperty()
    {
        return $this->searchResult ? $this->searchResult->count() : 0;
    }

    public function getHasActiveFiltersProperty()
    {
        return !empty($this->selectedPriceFilters) || !empty($this->selectedCategoryFilters);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('home', [
            // Core data
            'promos' => $this->promos,
            'favorites' => $this->favorites,
            'foodsByCategory' => $this->foodsByCategory,
            'categories' => $this->categories,
            'searchResult' => $this->searchResult,
            
            // UI states
            'isLoading' => $this->isLoading,
            'showGridView' => $this->showGridView,
            'selectedFilter' => $this->selectedFilter,
            'isCustomerDataComplete' => $this->isCustomerDataComplete,
            'filterModalOpen' => $this->filterModalOpen,
            
            // Filter states
            'selectedPriceFilters' => $this->selectedPriceFilters,
            'selectedCategoryFilters' => $this->selectedCategoryFilters,
            
            // Computed properties
            'hasPromos' => $this->hasPromos,
            'hasFavorites' => $this->hasFavorites,
            'hasFoodsByCategory' => $this->hasFoodsByCategory,
            'isSearching' => $this->isSearching,
            'searchResultCount' => $this->searchResultCount,
            'hasActiveFilters' => $this->hasActiveFilters,
            
            // User data
            'tableNumber' => $this->tableNumber,
            'term' => $this->term
        ]);
    }
}