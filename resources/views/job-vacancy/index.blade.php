<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{--? check if archived or not to display the correct title --}}
            @if (request()->has('archived'))
                {{ __('Archived vacancies') }}      
            @else
                {{ __('Job Vacancies') }}     
            @endif
        </h2>
    </x-slot>

    {{--! ============================== ACTION BUTTONS AND SEARCH ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--! search form  --}}
        <x-search :route="'job-vacancy.index'" :placeholder="__('Search for a job vacancy')" />

        {{--! action buttons  --}}
        <x-action-buttons :indexRoute="route('job-vacancy.index')" :addRoute="route('job-vacancy.create')" />
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
                    <x-bulk-restore :route="route('job-vacancy.bulkRestore')" />
                    <x-bulk-permenant-delete :route="route('job-vacancy.bulkDestroy')" />      
                </div>
            @else
                <x-bulk-delete :route="route('job-vacancy.bulkDestroy')" />                
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
                                    ? {{ $JobVacancies->pluck('id') }} 
                                    : []
                            "/> 
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Title
                        </th>
                        {{--? check if user is admin --}}
                        @if (Auth::user()->role == 'admin') 
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Company
                        </th>
                        @endif
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            salary
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                {{--! table body --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ( $JobVacancies as $JobVacancy )
                    
                    <tr class="hover:bg-indigo-50">
                        {{--! checkbox --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <x-select :value="$JobVacancy->id"  x-model="items" @change="selectAll = items.length === {{ $JobVacancies->count() }}"  />
                            </div>
                        </td>


                        <td class="px-6 py-4 whitespace-nowrap">
                            {{--! applicant name --}}
                            <div class="text-sm text-gray-900">
    
                                {{--? to display the name as link in index and without link if archived --}}
                                @if (request()->has('archived'))
                                <span class="text-gray-500">{{ $JobVacancy->title }}</span>
                                @else
                                <a class="text-blue-600 hover:underline" href="{{ route('job-vacancy.show', $JobVacancy->id) }}">
                                    {{ $JobVacancy->title }}
                                </a>
                                @endif
                            </div>
                        </td>
                        {{--! company name --}}
                        @if (Auth::user()->role == 'admin') 
                        <td class=" flex items-center px-6 py-4 whitespace-nowrap">
                            <x-company-logo :logoUri="$JobVacancy->company->logoUri" :logoName="$JobVacancy->company->name" :companyName="$JobVacancy->company->name" class="w-8 h-8" />
                            <div class="text-sm text-gray-900">{{ $JobVacancy->company->name }}</div>
                        </td>
                        @endif
                        {{--! location --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $JobVacancy->location }}</div>
                        </td>
                        {{--! type --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $JobVacancy->type }}</div>
                        </td>
                        {{--! salary --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${{ number_format($JobVacancy->salary,2) }}</div>
                        </td>
                        {{--! actions buttons --}}
                        <td class="px-6 py-4 whitespace-nowrap  text-sm font-medium">
                            {{--! archived JobVacancy buttons --}}
                            @if (request()->has('archived'))
                            <form action="{{ route('job-vacancy.restore', $JobVacancy->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
                            </form>
                            <form action="{{ route('job-vacancy.destroy', $JobVacancy->id) }}?archived=true" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure you want to delete this job vacancy permanently?')" type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Permanently Delete</button>
                            </form>
                            {{--! active JobVacancy buttons --}}
                            @else
                            <a href="{{ route('job-vacancy.edit', $JobVacancy->id) }}" class="text-indigo-600 hover:text-indigo-900">✍🏻 Edit</a>
                            <form action="{{ route('job-vacancy.destroy', $JobVacancy->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Delete </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    {{--! if no applications found --}}
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            No job-vacancies found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{--! pagination links --}}
            <div class="my-4">
                {{ $JobVacancies->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
