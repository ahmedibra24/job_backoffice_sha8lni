<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{--? check if archived or not to display the correct title --}}
            @if (request()->has('archived'))
                {{ __('Archived Categories') }}      
            @else
                {{ __('Job Categories') }}     
            @endif
        </h2>
    </x-slot>

    {{--! ============================== ACTION BUTTONS AND SEARCH ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--! search form  --}}
        <x-search :route="'category.index'" :placeholder="__('Search for a category')" />

        {{--! action buttons  --}}
        <x-action-buttons :indexRoute="route('category.index')" :addRoute="route('category.create')" />
    </div>


    {{--! show notification  --}}
    <x-notification-message />

    {{--! ============================== TABLE CONTENT ====================================== --}}
    <div x-data="{  items: [], selectAll: false}">
        {{-- ! bulk delete form if selected --}}
        <div style="display: none;" x-show="items.length > 0" x-clock x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="transform opacity-0" x-transition:enter-end="transform opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="transform opacity-100" x-transition:leave-end="transform opacity-0" class="mb-4 px-6 py-3 bg-gray-100 rounded-md flex items-center justify-end gap-x-3">
             {{--? if archived show restore and permenant delete buttons otherwise show delete button --}}
            @if (request()->has('archived'))
                <div class="flex gap-3 ">
                    <x-bulk-restore :route="route('category.bulkRestore')" />
                    <x-bulk-permenant-delete :route="route('category.bulkDestroy')" />      
                </div>
            @else
                <x-bulk-delete :route="route('category.bulkDestroy')" />                
            @endif
        </div>
        <div class="overflow-x-auto px-6">
            {{--! table header --}}
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <x-select x-model="selectAll"  @change="
                                items = selectAll 
                                    ? {{ $categories->pluck('id') }} 
                                    : []
                            "/> 
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                {{--! table body --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ( $categories as $category )
                    
                    <tr class="hover:bg-indigo-50">
                        {{--! checkbox --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <x-select :value="$category->id"  x-model="items" @change="selectAll = items.length === {{ $categories->count() }}"  />
                            </div>
                        </td>

                        {{--! category name --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $category->name }}</div>
                        </td>
                        {{--! actions buttons --}}
                        <td class="px-6 py-4 whitespace-nowrap  text-sm font-medium">
                            {{--! archived category buttons --}}
                            @if (request()->has('archived'))
                            <form action="{{ route('category.restore', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
                            </form>
                            <form action="{{ route('category.destroy', $category->id) }}?archived=true" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure you want to delete this category permanently?')" type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Permanently Delete</button>
                            </form>
                            {{--! active category buttons --}}
                            @else
                            <a href="{{ route('category.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900">✍🏻 Edit</a>
                            <form action="{{ route('category.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Delete </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    {{--! if no categories found --}}
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{--! pagination links --}}
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
