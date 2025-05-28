<div 
    x-data="{ show: true }" 
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
    >
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Lengkapi Pemesanan</h2>
        
        <form wire:submit.prevent="saveUserInfo">
            <div class="mb-6 mt-4 space-y-4">
                <div class="flex flex-col space-y-1">
                    <label
                        class="text-xs font-semibold text-black-50"
                        for="name"
                    >
                        Nama Pemesan
                    </label>
                    <input
                        class="{{ $errors->has('name') ? 'border-red-500' : 'border-black-30' }} rounded-lg border px-2 py-1.5"
                        type="text"
                        name="name"
                        wire:model.live="name"
                    />
                    @error('name')
                        <span class="text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="flex flex-col space-y-1">
                    <label
                        class="text-xs font-semibold text-black-50"
                        for="phone"
                    >
                        Nomor Handphone
                    </label>
                    <input
                        class="{{ $errors->has('phone') ? 'border-red-500' : 'border-black-30' }} rounded-lg border px-2 py-1.5"
                        type="tel"
                        name="phone"
                        wire:model.live="phone"
                    />
                    @error('phone')
                        <span class="text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button
                    type="button"
                    class="cursor-pointer rounded-full bg-primary-10 px-5 py-2 font-semibold text-primary-60 outline-none hover:bg-primary-20"
                    disabled
                >
                    Kembali
                </button>
                <button
                    type="submit"
                    class="cursor-pointer rounded-full bg-primary-50 px-5 py-2 font-semibold text-white hover:bg-primary-60"
                >
                    <span class="flex items-center gap-1.5">
                        Terapkan
                        <img
                            src="{{ asset('assets/icons/arrow-right-white-icon.svg') }}"
                            alt="Terapkan"
                        />
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>