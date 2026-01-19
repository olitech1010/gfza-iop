<x-filament-panels::page>
    @if($this->currentMenu)
        <div class="space-y-6">
            {{-- Menu Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->currentMenu->week_label ?? 'This Week\'s Menu' }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Caterer: <strong>{{ $this->currentMenu->caterer->name }}</strong>
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $this->currentMenu->week_start->format('M j') }} - {{ $this->currentMenu->week_end->format('M j, Y') }}
                        </p>
                    </div>
                    @if(auth()->user()->is_nss)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <x-heroicon-s-academic-cap class="w-4 h-4 mr-1" />
                            NSS - No Payment Required
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                            GHS 5.00 per meal
                        </span>
                    @endif
                </div>
            </div>

            {{-- Meal Selection by Day --}}
            <form wire:submit="submitSelections" class="space-y-4">
                @foreach($this->getMenuByDay() as $day => $meals)
                    @if($meals->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 capitalize">
                                {{ $day }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($meals as $menuItem)
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none
                                        {{ isset($this->selections[$day]) && $this->selections[$day] == $menuItem->id 
                                            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
                                            : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        <input type="radio" 
                                               name="selections[{{ $day }}]" 
                                               value="{{ $menuItem->id }}"
                                               wire:model.live="selections.{{ $day }}"
                                               class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $menuItem->mealItem->name }}
                                                </span>
                                                @if($menuItem->mealItem->description)
                                                    <span class="mt-1 text-xs text-gray-500">
                                                        {{ $menuItem->mealItem->description }}
                                                    </span>
                                                @endif
                                            </span>
                                        </span>
                                        @if(isset($this->selections[$day]) && $this->selections[$day] == $menuItem->id)
                                            <x-heroicon-s-check-circle class="h-5 w-5 text-primary-600" />
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Selection Preview --}}
                @php $preview = $this->getSelectedMealsPreview(); @endphp
                @if(count($preview) > 0)
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-3">
                            <x-heroicon-o-eye class="w-5 h-5 inline mr-2" />
                            Your Selection Preview
                        </h3>
                        <div class="space-y-2">
                            @foreach($preview as $day => $mealName)
                                <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700 last:border-0">
                                    <span class="font-medium capitalize text-green-700 dark:text-green-300">{{ $day }}</span>
                                    <span class="text-green-900 dark:text-green-100">{{ $mealName }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-700">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-green-800 dark:text-green-200">Total Meals:</span>
                                <span class="text-lg font-bold text-green-900 dark:text-green-100">{{ count($preview) }}</span>
                            </div>
                            @if(!auth()->user()->is_nss)
                                <div class="flex justify-between items-center mt-2">
                                    <span class="font-bold text-green-800 dark:text-green-200">Total Amount:</span>
                                    <span class="text-lg font-bold text-green-900 dark:text-green-100">GHS {{ number_format(count($preview) * 5, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-heroicon-o-check class="w-5 h-5 mr-2" />
                        Save My Selections
                    </x-filament::button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl shadow p-8 text-center">
            <x-heroicon-o-calendar class="w-16 h-16 mx-auto text-yellow-500 mb-4" />
            <h2 class="text-xl font-semibold text-yellow-800 dark:text-yellow-200">No Menu Available</h2>
            <p class="text-yellow-600 dark:text-yellow-400 mt-2">
                There is no published menu for this week. Please check back later.
            </p>
        </div>
    @endif
</x-filament-panels::page>
