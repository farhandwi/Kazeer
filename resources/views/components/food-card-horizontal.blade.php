<div class="min-w-[250px] max-w-[250px] bg-white rounded-2xl shadow-md overflow-hidden relative hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
    <!-- Food Image -->
    <div class="relative w-full h-36 overflow-hidden">
        <img 
            src="{{ $data->image ? asset('storage/' . $data->image) : asset('storage/default-food.jpg') }}"
            alt="{{ $data->name }}" 
            class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-110"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
        >

        <!-- Fallback gradient background -->
        <div class="w-full h-full bg-gradient-to-br from-orange-400 via-red-500 to-pink-500 flex items-center justify-center hidden">
            <div class="text-center text-white">
                <div class="text-lg font-bold mb-1">{{ substr($data->name, 0, 8) }}</div>
                <div class="text-xs opacity-80">Delicious Food</div>
            </div>
        </div>

        <!-- Discount Badge -->
        @if($data->percent ?? false)
            <div class="absolute top-3 left-3 bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg animate-pulse">
                <div class="flex items-center space-x-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $data->percent }}% OFF</span>
                </div>
            </div>
        @endif

        <!-- Favorite Button -->
        <button class="absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-110 group-hover:bg-red-50">
            <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </button>

        <!-- Premium Badge (if applicable) -->
        @if($data->is_premium ?? false)
            <div class="absolute bottom-3 left-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-semibold px-2 py-1 rounded-full flex items-center space-x-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span>Premium</span>
            </div>
        @endif
    </div>

    <!-- Food Info -->
    <div class="p-4">
        <!-- Name with rating -->
        <div class="flex items-start justify-between mb-2">
            <h3 class="font-semibold text-gray-900 text-sm leading-tight line-clamp-2 flex-1 pr-2">
                {{ $data->name }}
            </h3>
            
            <!-- Rating -->
            @if($data->rating ?? false)
                <div class="flex items-center space-x-1 flex-shrink-0">
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs font-medium text-gray-600">{{ $data->rating }}</span>
                </div>
            @endif
        </div>

        <!-- Description (if available) -->
        @if($data->description ?? false)
            <p class="text-gray-500 text-xs leading-relaxed mb-3 line-clamp-2">
                {{ Str::limit($data->description, 60) }}
            </p>
        @endif

        <!-- Price Section -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    @if($data->price && $data->price > $data->price_afterdiscount)
                        <div class="text-gray-400 line-through text-xs mb-1 flex items-center">
                            <span class="mr-1">Rp</span>
                            <span>{{ number_format($data->price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                
                    <div class="text-orange-600 font-bold text-base flex items-center">
                        <span class="mr-1">Rp</span>
                        <span>{{ number_format($data->price_afterdiscount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Savings Badge -->
                @if($data->price && $data->price > $data->price_afterdiscount)
                    <div class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-md">
                        Hemat Rp{{ number_format($data->price - $data->price_afterdiscount, 0, ',', '.') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Category and Availability -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-1">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-gray-500 text-xs font-medium">
                    {{ $data->categories_id->name ?? 'Kategori' }}
                </span>
            </div>

            <!-- Stock Status -->
            @if($data->stock ?? true)
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-green-600 text-xs font-medium">Tersedia</span>
                </div>
            @else
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                    <span class="text-red-600 text-xs font-medium">Habis</span>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <!-- Primary Action Buttons -->
            <div class="flex gap-2">
                <!-- Add to Cart Button -->
                <button 
                    wire:click="addToCart({{ $data->id }})"
                    class="flex-1 bg-white border-2 border-orange-500 text-orange-500 hover:bg-orange-50 disabled:border-gray-300 disabled:text-gray-400 font-semibold py-2.5 px-3 rounded-xl text-sm transition-all duration-200 transform hover:scale-[1.02] disabled:hover:scale-100 shadow-sm"
                    {{ ($data->stock ?? true) ? '' : 'disabled' }}
                >
                    <div class="flex items-center justify-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L12 17m0 0l2.5-1L12 17"></path>
                        </svg>
                        <span>Cart</span>
                    </div>
                </button>

                <!-- Order Now Button -->
                <button 
                    wire:click="orderNow({{ $data->id }})"
                    class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-semibold py-2.5 px-3 rounded-xl text-sm transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg disabled:hover:scale-100 disabled:shadow-md"
                    {{ ($data->stock ?? true) ? '' : 'disabled' }}
                >
                    <div class="flex items-center justify-center space-x-1.5">
                        @if($data->stock ?? true)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span>Pesan</span>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                            </svg>
                            <span>Habis</span>
                        @endif
                    </div>
                </button>
            </div>

            <!-- View Details Button -->
            <button 
                wire:click="showDetails({{ $data->id }})"
                class="w-full bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-900 font-medium py-2 px-3 rounded-xl text-sm transition-all duration-200 border border-gray-200 hover:border-gray-300"
            >
                <div class="flex items-center justify-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Lihat Detail</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Loading Overlay (when processing actions) -->
    <div class="absolute inset-0 bg-white/90 backdrop-blur-sm rounded-2xl flex items-center justify-center opacity-0 transition-opacity duration-200 pointer-events-none" wire:loading.class="opacity-100 pointer-events-auto">
        <div class="flex flex-col items-center space-y-2">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
            <span class="text-sm font-medium text-gray-600">Memproses...</span>
        </div>
    </div>
</div>