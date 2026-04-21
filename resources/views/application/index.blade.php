<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            {{--? check if archived or not to display the correct title --}}
            @if (request()->has('archived'))
            {{ __('Archived applications') }}      
            @else
            {{ __('Job applications') }}     
            @endif
        </h2>
    </x-slot>
    
    {{--! ============================== ACTION BUTTONS, SEARCH AND FILTER ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--! search form  --}}
        <x-search :route="'application.index'" :placeholder="__('Search for an application')" />

        {{--! action buttons  --}}
        <x-action-buttons :indexRoute="route('application.index')" />
    </div>

    {{--! status filter --}}
    <div class="mr-6 mb-3 flex items-center justify-end gap-x-3">
        <x-filter :route="'application.index'" :options="['pending', 'accepted', 'rejected']" />
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
                        <x-bulk-restore :route="route('application.bulkRestore')" />
                        <x-bulk-permenant-delete :route="route('application.bulkDestroy')" />      
                    </div>
                @else
                    <x-bulk-delete :route="route('application.bulkDestroy')" />                
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
                                    ? {{ $applications->pluck('id') }} 
                                    : []
                            "/> 
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Applicant Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position (job vacancy)
                        </th>
    
                        {{--? check if user is admin --}}
                        @if (Auth::user()->role == 'admin')  
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Company
                        </th>
                        @endif
    
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                {{--! table body --}}
                <tbody class="bg-white divide-y divide-gray-200">
    
                    {{--? forelse -> to show applications or a message if no applications exist --}}
                    @forelse ( $applications as $application )              
                    <tr class="hover:bg-indigo-50">
                        {{--! checkbox --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <x-select :value="$application->id"  x-model="items" @change="selectAll = items.length === {{ $applications->count() }}"  />
                            </div>
                        </td>
                        {{--! applicant name --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
    
                                {{--? to display the name as link in index and without link if archived --}}
                                @if (request()->has('archived'))
                                <span class="text-gray-500">{{ $application->applicant->name }}</span>
                                @else
                                <a class="text-blue-600 hover:underline" href="{{ route('application.show', $application->id) }}">
                                    {{ $application->applicant->name }}
                                </a>
                                @endif
                            </div>
                        </td>
                        {{--! job vacancy title --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->jobVacancy?->title ??'N/A'}}</div>
                        </td>
                        {{--! company name --}}
                        {{--? show for admin only --}}
                        @if (Auth::user()->role == 'admin') 
                        <td class=" flex items-center px-6 py-4 whitespace-nowrap">
                            <x-company-logo :logoUri="$application->jobVacancy?->company->logoUri" :logoName="$application->jobVacancy?->company->name" :companyName="$application->jobVacancy?->company->name" class="w-8 h-8" />
                            <div class="text-sm text-gray-900">{{ $application->jobVacancy?->company->name??'N/A' }}</div>
                        </td>
                        @endif 
                        {{--! status --}}                  
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class=" font-semibold text-sm text-center p-1 rounded-lg{{ $application->status == 'pending' ? ' bg-yellow-100 text-yellow-800' : ($application->status == 'accepted' ? ' bg-green-100 text-green-800' : ' bg-red-100 text-red-800') }}">{{ $application->status }}</div>
                        </td>
                        {{--! actions buttons --}}
                        <td class="px-6 py-4 whitespace-nowrap  text-sm font-medium">
                            {{--! archived applications buttons --}}
                            @if (request()->has('archived'))
                            <form action="{{ route('application.restore', $application->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
                            </form>
                            <form action="{{ route('application.destroy', $application->id) }}?archived=true" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure you want to delete application permanently?')" type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Permanently Delete</button>
                            </form>
                            {{--! active applications buttons --}}
                            @else
                            <a href="{{ route('application.edit', $application->id) }}" class="text-indigo-600 hover:text-indigo-900">✍🏻 Edit</a>
                            <form action="{{ route('application.destroy', $application->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Delete </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    {{--! if no applications found --}}
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            No job-applications found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{--! pagination links --}}
            <div class="my-4">
                {{ $applications->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
