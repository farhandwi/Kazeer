<div class="{{ $showGridView ? 'bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow mt-8' : 'bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow flex' }}">
    <!-- Food Image -->
    <div class="{{ $showGridView ? 'relative w-full h-32 overflow-hidden rounded-t-2xl' : 'relative w-24 h-24 overflow-hidden rounded-l-2xl flex-shrink-0' }}">
        <img src="{{ $data->image ? asset('storage/' . $data->image) : asset('storage/default-food.jpg') }}" 
            alt="{{ $data->name }}" 
            class="w-full h-full object-cover object-center"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">

        <!-- Fallback gradient background -->
        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center hidden">
            <span class="text-white font-bold text-xs">{{ substr($data->name, 0, 8) }}</span>
        </div>

        <!-- Discount Badge -->
        @if($data->discount ?? false)
            <div class="absolute top-2 {{ $showGridView ? 'right-2' : 'right-1' }} bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md">
                {{ $data->discount }}%
            </div>
        @endif
    </div>

    <!-- Food Info -->
    <div class="{{ $showGridView ? 'p-4' : 'p-3 flex-1 flex flex-col justify-between' }}">
        <div class="{{ $showGridView ? 'mb-4' : 'mb-2' }}">
            <h3 class="font-semibold text-gray-900 {{ $showGridView ? 'text-sm' : 'text-sm' }} mb-1 line-clamp-2">{{ $data->name }}</h3>
            
            <!-- Price -->
            <div class="mb-2">
                @if($data->price && $data->price > $data->price_afterdiscount)
                    <div class="text-gray-400 line-through text-xs">Rp {{ number_format($data->price, 0, ',', '.') }}</div>
                @endif
                <div class="text-orange-500 font-bold {{ $showGridView ? 'text-sm' : 'text-sm' }}">Rp {{ number_format($data->price_afterdiscount, 0, ',', '.') }}</div>
            </div>
            
            <!-- Category -->
            @if($data->categories_id)
                <p class="text-gray-500 text-xs {{ $showGridView ? '' : '' }}">{{ $data->categories_id->name ?? 'Tersedia' }}</p>
            @else
                <p class="text-gray-500 text-xs {{ $showGridView ? '' : '' }}">Tersedia</p>
            @endif

            <!-- Stock Status -->
            @if($showGridView)
                <div class="flex items-center space-x-1 mt-1">
                    @if($data->stock ?? true)
                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full"></div>
                        <span class="text-green-600 text-xs font-medium">Tersedia</span>
                    @else
                        <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                        <span class="text-red-600 text-xs font-medium">Habis</span>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Action Buttons -->
        @if($showGridView)
            <!-- Grid View Buttons -->
            <div class="space-y-2">
                <div class="flex gap-2">
                    <!-- Add to Cart -->
                    <button 
                        wire:click="addToCart({{ $data->id }})"
                        class="flex-1 bg-white border-2 border-orange-500 text-orange-500 hover:bg-orange-50 disabled:border-gray-300 disabled:text-gray-400 font-medium py-2 px-2 rounded-lg text-xs transition-all duration-200 disabled:cursor-not-allowed"
                        {{ ($data->stock ?? true) ? '' : 'disabled' }}
                    >
                        <div class="flex items-center justify-center space-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L12 17m0 0l2.5-1L12 17"></path>
                            </svg>
                            <span>Cart</span>
                        </div>
                    </button>

                    <!-- Order Now -->
                    <button 
                        wire:click="orderNow({{ $data->id }})"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-medium py-2 px-2 rounded-lg text-xs transition-all duration-200 disabled:cursor-not-allowed"
                        {{ ($data->stock ?? true) ? '' : 'disabled' }}
                    >
                        <div class="flex items-center justify-center space-x-1">
                            @if($data->stock ?? true)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span>Pesan</span>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                <span>Habis</span>
                            @endif
                        </div>
                    </button>
                </div>

                <!-- View Details -->
                <button 
                    wire:click="showDetails({{ $data->id }})"
                    class="w-full bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-800 font-medium py-2 px-2 rounded-lg text-xs transition-all duration-200 border border-gray-200"
                >
                    <div class="flex items-center justify-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>Lihat</span>
                    </div>
                </button>
            </div>
        @else
            <!-- List View Buttons -->
            <div class="flex flex-col space-y-2">
                <div class="flex gap-1.5">
                    <!-- Add to Cart -->
                    <button 
                        wire:click="addToCart({{ $data->id }})"
                        class="flex-1 bg-white border border-orange-500 text-orange-500 hover:bg-orange-50 disabled:border-gray-300 disabled:text-gray-400 font-medium py-1.5 px-2 rounded-md text-xs transition-all duration-200 disabled:cursor-not-allowed"
                        {{ ($data->stock ?? true) ? '' : 'disabled' }}
                    >
                        <div class="flex items-center justify-center space-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13"></path>
                            </svg>
                            <span>Cart</span>
                        </div>
                    </button>

                    <!-- Order Now -->
                    <button 
                        wire:click="orderNow({{ $data->id }})"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-gray-300 text-white font-medium py-1.5 px-2 rounded-md text-xs transition-all duration-200 disabled:cursor-not-allowed"
                        {{ ($data->stock ?? true) ? '' : 'disabled' }}
                    >
                        @if($data->stock ?? true)
                            <span>Pesan</span>
                        @else
                            <span>Habis</span>
                        @endif
                    </button>
                </div>

                <!-- View Details -->
                <button 
                    wire:click="showDetails({{ $data->id }})"
                    class="w-full bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-800 font-medium py-1.5 px-2 rounded-md text-xs transition-all duration-200"
                >
                    <div class="flex items-center justify-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Lihat</span>
                    </div>
                </button>

                <!-- Stock Status for List View -->
                <div class="flex items-center space-x-1 justify-center">
                    @if($data->stock ?? true)
                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full"></div>
                        <span class="text-green-600 text-xs font-medium">Tersedia</span>
                    @else
                        <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                        <span class="text-red-600 text-xs font-medium">Habis</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Loading Overlay for Flexible Card -->
    <div class="absolute inset-0 bg-white/90 backdrop-blur-sm rounded-2xl flex items-center justify-center opacity-0 transition-opacity duration-200 pointer-events-none" wire:loading.class="opacity-100 pointer-events-auto">
        <div class="flex flex-col items-center space-y-1">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500"></div>
            <span class="text-xs font-medium text-gray-600">Loading...</span>
        </div>
    </div>
</div>