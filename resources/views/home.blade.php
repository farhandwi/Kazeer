{{-- resources/views/home.blade.php --}}
<div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false, showGridView: @entangle('showGridView') }">
    
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" 
             x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg" 
             x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('error') }}
        </div>
    @endif

    <!-- Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-40" 
         @click="sidebarOpen = false">
        <div class="fixed left-0 top-0 h-full w-80 bg-white shadow-2xl z-50"
             x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform -translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform -translate-x-full"
             @click.stop>
            <div class="p-6">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Menu</h2>
                        <p class="text-sm text-gray-500">{{ $tableNumber ? 'Meja ' . $tableNumber : 'No Table' }}</p>
                    </div>
                    <button @click="sidebarOpen = false" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Menu Items -->
                <nav class="space-y-2">
                    <a href="#" class="flex items-center gap-4 p-3 hover:bg-orange-50 rounded-xl transition-colors group">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Informasi Toko</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-4 p-3 hover:bg-orange-50 rounded-xl transition-colors group">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Syarat & Ketentuan</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-4 p-3 hover:bg-orange-50 rounded-xl transition-colors group">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Bahasa</span>
                    </a>

                    <!-- Refresh Data Button -->
                    <button wire:click="refreshData" class="w-full flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition-colors group">
                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Refresh Data</span>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="px-4 py-4">
            <!-- Top Navigation Bar -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <button class="p-2.5 bg-orange-500 rounded-full text-white shadow-md hover:bg-orange-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div class="flex flex-col">
                        <h1 class="text-xl font-bold text-gray-900 leading-tight">Shou Dimsum</h1>
                        <p class="text-sm text-gray-600 font-medium">Sleman, Yogyakarta</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Notification Badge -->
                    <div class="relative">
                        <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center border border-orange-100 hover:bg-orange-100 transition-colors cursor-pointer">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7V3a2 2 0 012-2h2a2 2 0 012 2v4"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-sm">2</span>
                    </div>
                    
                    <!-- Menu Button -->  
                    <button @click="sidebarOpen = true" class="p-2.5 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Restaurant Image -->
            <div class="mb-6">
                <div class="relative overflow-hidden rounded-2xl shadow-lg">
                    <div class="w-full h-48 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                        <div class="text-white text-center">
                            <h3 class="text-2xl font-bold mb-2">Shou Dimsum</h3>
                            <p class="text-orange-100">Authentic Chinese Cuisine</p>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
            </div>

            <!-- Filter and Search Section -->
            <div class="flex items-stretch gap-3">
                <button 
                    wire:click="showFilterModal" 
                    wire:loading.attr="disabled"
                    class="relative flex items-center justify-center gap-2 bg-gradient-to-r from-orange-400 to-yellow-400 text-white px-5 py-3 rounded-full font-semibold text-sm shadow-md hover:shadow-lg hover:from-orange-500 hover:to-yellow-500 transition-all duration-300 whitespace-nowrap disabled:opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <p class="text-sm text-white">Filter</p>
                    
                    <!-- Active Filter Indicator -->
                    @if($hasActiveFilters)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-sm">
                            {{ count($selectedPriceFilters) + count($selectedCategoryFilters) }}
                        </span>
                    @endif
                </button>
                
                <div class="flex-1 relative">
                    <!-- Icon -->
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 transition-opacity duration-200 peer-focus:opacity-0" id="search-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                
                    <!-- Input -->
                    <input 
                        id="searchInput"
                        type="search"
                        class="peer w-full h-full pl-12 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-full focus:bg-white focus:border-orange-300 focus:ring-2 focus:ring-orange-200 focus:outline-none transition-all duration-200 placeholder-gray-500 font-medium"
                        placeholder="Cari Makanan"
                        wire:model.live.debounce.300ms="term"
                        autocomplete="off"
                    />
                </div>                
            </div>
        </div>
    </header>

    <!-- Modal Customer Data -->
    @if ($isCustomerDataComplete)
        <livewire:components.customer-modal />
    @endif

    <!-- Main Content -->
    <main class="px-4 py-6 pb-24">
        <!-- Loading State -->
        <div wire:loading.flex wire:target="term" class="justify-center py-8">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500"></div>
                <p class="text-gray-500">Mencari makanan...</p>
            </div>
        </div>
        
        <div wire:loading.remove wire:target="term">
            @if (!$isSearching)
                <!-- Promo Terbatas Section -->
                @if ($hasPromos)
                    <section class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Promo Terbatas</h2>
                            <a href="/food/promo" wire:navigate class="text-orange-500 font-medium hover:text-orange-600 transition-colors">See More</a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <div class="flex gap-4 px-2 py-3">
                                @foreach ($promos as $promo)
                                    <div class="flex-shrink-0 w-64">
                                        <livewire:components.food-card-horizontal
                                            wire:key="promo-{{ $promo->id }}"
                                            :data="$promo"
                                            :categories="$categories"
                                        />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Main Course Section -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Main Course</h2>
                        <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                            <button 
                                wire:click="toggleGridView"
                                :class="!showGridView ? 'bg-white text-orange-500 shadow-sm' : 'text-gray-500 hover:text-orange-500'"
                                class="p-2 rounded-md transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="toggleGridView"
                                :class="showGridView ? 'bg-white text-orange-500 shadow-sm' : 'text-gray-500 hover:text-orange-500'"
                                class="p-2 rounded-md transition-all duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Food Grid/List Container -->
                    <div :class="showGridView ? 'grid grid-cols-2 gap-4' : 'space-y-4'">
                        <!-- Favorites Section -->
                        @if ($hasFavorites)
                            @foreach ($favorites as $favorite)
                                <livewire:components.food-card-main
                                    wire:key="favorite-{{ $favorite->id }}"
                                    :data="$favorite"
                                    :categories="$categories"
                                    :showGridView="$showGridView"
                                />
                            @endforeach
                        @endif

                        <!-- Foods by Category -->
                        @if ($hasFoodsByCategory)
                            @foreach ($foodsByCategory as $category)
                            <div class="category-section" wire:key="category-{{ $category['id'] }}">
                                @if (isset($category['foods']) && count($category['foods']) > 0)
                                    @foreach ($category['foods'] as $food)
                                        <livewire:components.food-card-main
                                            wire:key="category-{{ $category['id'] }}-food-{{ $food->id }}"
                                            :data="$food"
                                            :categories="$categories"
                                            :showGridView="$showGridView"
                                        />
                                    @endforeach
                                @endif
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Empty State -->
                    @if (!$hasFavorites && !$hasFoodsByCategory)
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium">Belum ada makanan tersedia</p>
                            <button wire:click="refreshData" class="mt-4 text-orange-500 hover:text-orange-600 font-medium">
                                Refresh Data
                            </button>
                        </div>
                    @endif
                </section>

            @else
                <!-- Search Results -->
                <section>
                    <div class="mb-4">
                        <p class="text-gray-600">
                            Ditemukan {{ $searchResultCount }} hasil untuk 
                            "<span class="font-semibold">{{ $term }}</span>"
                        </p>
                    </div>

                    @if ($searchResultCount > 0)
                        <div :class="showGridView ? 'grid grid-cols-2 gap-4' : 'space-y-4'">
                            @foreach ($searchResult as $result)
                                <livewire:components.food-card-main
                                    wire:key="search-{{ $result->id }}-{{ str()->random(10) }}"
                                    :data="$result"
                                    :categories="$categories"
                                    :showGridView="$showGridView"
                                />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium">Makanan tidak ditemukan</p>
                            <p class="text-gray-400 text-sm mt-2">Coba gunakan kata kunci yang berbeda</p>
                            <button wire:click="$set('term', '')" class="mt-4 text-orange-500 hover:text-orange-600 font-medium">
                                Lihat Semua Menu
                            </button>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </main>

    <!-- Filter Modal -->
    @if ($filterModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end" 
             wire:key="filter-modal"
             @click.self="$wire.closeFilterModal()">
             <div class="bg-white rounded-t-2xl w-full sm:max-h-[90vh] max-h-[90vh] overflow-hidden"
                 @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Filter Menu</h3>
                    <button 
                        wire:click="closeFilterModal" 
                        type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-4 overflow-y-auto max-h-[calc(80vh-120px)] pb-32">
                    
                    <!-- Price Filter Section -->
                    <div class="mb-6">
                        <h4 class="text-base font-medium text-gray-900 mb-3">Sesuaikan Harga</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                                 wire:click="togglePriceFilter('termurah')">
                                <span class="text-gray-700">Termurah</span>
                                <div class="w-5 h-5 rounded border-2 border-orange-300 flex items-center justify-center transition-colors {{ in_array('termurah', $selectedPriceFilters) ? 'bg-orange-500 border-orange-500' : '' }}">
                                    @if(in_array('termurah', $selectedPriceFilters))
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                                 wire:click="togglePriceFilter('termahal')">
                                <span class="text-gray-700">Termahal</span>
                                <div class="w-5 h-5 rounded border-2 border-orange-300 flex items-center justify-center transition-colors {{ in_array('termahal', $selectedPriceFilters) ? 'bg-orange-500 border-orange-500' : '' }}">
                                    @if(in_array('termahal', $selectedPriceFilters))
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                                 wire:click="togglePriceFilter('diskon')">
                                <span class="text-gray-700">Diskon</span>
                                <div class="w-5 h-5 rounded border-2 border-orange-300 flex items-center justify-center transition-colors {{ in_array('diskon', $selectedPriceFilters) ? 'bg-orange-500 border-orange-500' : '' }}">
                                    @if(in_array('diskon', $selectedPriceFilters))
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                                 wire:click="togglePriceFilter('terfavorit')">
                                <span class="text-gray-700">Terfavorit</span>
                                <div class="w-5 h-5 rounded border-2 border-orange-300 flex items-center justify-center transition-colors {{ in_array('terfavorit', $selectedPriceFilters) ? 'bg-orange-500 border-orange-500' : '' }}">
                                    @if(in_array('terfavorit', $selectedPriceFilters))
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter Section -->
                    <div class="mb-6">
                        <h4 class="text-base font-medium text-gray-900 mb-3">Sesuaikan Menu</h4>
                        <div class="space-y-2">
                            @foreach ($categories as $category)
                                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                                     wire:click="toggleCategoryFilter({{ $category->id }})">
                                    <span class="text-gray-700">{{ $category->name }}</span>
                                    <div class="w-5 h-5 rounded border-2 border-orange-300 flex items-center justify-center transition-colors {{ in_array($category->id, $selectedCategoryFilters) ? 'bg-orange-500 border-orange-500' : '' }}">
                                        @if(in_array($category->id, $selectedCategoryFilters))
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-gray-100 bg-white shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
                    <div class="flex space-x-3">
                        @if($hasActiveFilters)
                            <button 
                                wire:click="clearFilters" 
                                type="button"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                Hapus Filter
                            </button>
                        @endif
                        <button 
                            wire:click="applyFilters" 
                            type="button"
                            class="flex-1 px-4 py-3 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-colors">
                            Sesuaikan Menu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease;
    }
    
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush