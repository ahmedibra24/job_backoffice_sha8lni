<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if (request()->has('archived'))
                {{ __('Archived Companies') }}      
            @else
                {{ __('Companies') }}     
            @endif
        </h2>
    </x-slot>
    {{--! ============================== ACTION BUTTONS AND SEARCH ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--! search form  --}}
        <x-search :route="'company.index'" :placeholder="__('Search for a company')" />

        {{--! action buttons  --}}
        <x-action-buttons :indexRoute="route('company.index')" :addRoute="route('company.create')" />
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
                        <x-bulk-restore :route="route('company.bulkRestore')" />
                        <x-bulk-permenant-delete :route="route('company.bulkDestroy')" />      
                    </div>
                @else
                    <x-bulk-delete :route="route('company.bulkDestroy')" />                
                @endif
            </div>

        <div class="overflow-x-auto px-6">
            <table class="min-w-full divide-y divide-gray-200">
                {{--! table header --}}
                <thead class="bg-indigo-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <x-select x-model="selectAll"  @change="
                                items = selectAll 
                                    ? {{ $Companies->pluck('id') }} 
                                    : []
                            "/> 
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Address
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Industry
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Website
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                {{--! table body --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ( $Companies as $Company )               
                    <tr class="hover:bg-indigo-50">
                        {{--! checkbox --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <x-select :value="$Company->id"  x-model="items" @change="selectAll = items.length === {{ $Companies->count() }}"  />
                            </div>
                        </td>
                        {{--! name --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class=" flex items-center text-sm text-gray-900">
                                {{--? to display the name as link in index and without link if archived --}}
                                @if (request()->has('archived'))
                                    <x-company-logo :logoUri="$Company->logoUri" :logoName="$Company->logoName" :companyName="$Company->name" class="h-8 w-8 mr-4" />
                                    <span class="text-gray-500">{{ $Company->name }}</span>
                                @else
                                    <x-company-logo :logoUri="$Company->logoUri" :logoName="$Company->logoName" :companyName="$Company->name" class="h-8 w-8 mr-4" />
                                    <a class="text-blue-600 hover:underline" href="{{ route('company.show', $Company->id) }}">
                                        {{ $Company->name }}
                                    </a>
                                @endif
                            </div>
                        </td>
                        {{--! address --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $Company->address }}</div>
                        </td>
                        {{--! industry --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $Company->industry }}</div>
                        </td>
                        {{--! website --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <a href="{{ $Company->website }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $Company->website }}
                                </a>
                            </div>
                        </td>  
                        {{--! actions buttons --}}               
                        <td class="px-6 py-4 whitespace-nowrap  text-sm font-medium">
                            {{--! archived company buttons --}}
                            @if (request()->has('archived'))
                            <form action="{{ route('company.restore', $Company->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                @if (! $Company->owner)
                                <span class="text-xs text-gray-500">the owner is deleted</span>
                                @else
                                <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
                                @endif
                            </form>
                            <form action="{{ route('company.destroy', $Company->id) }}?archived=true" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure you want to delete this company permanently?')" type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Permanently Delete</button>
                            </form>
                            {{--! active company buttons --}}
                            @else
                            <a href="{{ route('company.edit', $Company->id) }}" class="text-indigo-600 hover:text-indigo-900">✍🏻 Edit</a>
                            <form action="{{ route('company.destroy', $Company->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Delete </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    {{--! if no companies found --}}
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            No companies found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{--! pagination links --}}
            <div class="mt-4">
                {{ $Companies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
