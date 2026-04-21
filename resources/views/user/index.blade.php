<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if (request()->has('archived'))
                {{ __('Archived users') }}      
            @else
                {{ __('Users') }}     
            @endif
        </h2>
    </x-slot>

    {{--! ============================== ACTION BUTTONS AND SEARCH ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--! search form  --}}
        <x-search :route="'user.index'" :placeholder="__('Search for a user')" />

        {{--! action buttons  --}}
        <x-action-buttons :indexRoute="route('user.index')" />
    </div>

    {{--! role filter --}}
    <div class="mr-6 mt-6 mb-3">
        <x-filter :route="'user.index'" :options="['recruiter','applicant']" />
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
                    <x-bulk-restore :route="route('user.bulkRestore')" />
                    <x-bulk-permenant-delete :route="route('user.bulkDestroy')" />      
                </div>
            @else
                <x-bulk-delete :route="route('user.bulkDestroy')" />                
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
                                    ? {{ $users->pluck('id') }} 
                                    : []
                            "/> 
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                {{--! table body --}}
                <tbody class="bg-white divide-y divide-gray-200">               
                    @forelse ( $users as $user )
                        @if ($user->role !=='admin')
                        <tr class="hover:bg-indigo-50">
                            {{--! checkbox --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <x-select :value="$user->id"  x-model="items" @change="selectAll = items.length === {{ $users->count() }}"  />
                                </div>
                            </td>

                            {{--! name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="text-gray-500">{{ $user->name }}</span>
                                </div>
                            </td>
                            {{--! email --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            {{--! role --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->role }}</div>
                            </td>
                            {{--! actions buttons --}}
                            <td class="px-6 py-4 whitespace-nowrap  text-sm font-medium">
                                {{--! archived users buttons --}}
                                @if (request()->has('archived'))
                                <form action="{{ route('user.restore', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
                                </form>
                                <form action="{{ route('user.destroy', $user->id) }}?archived=true" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure you want to delete user permanently?')" type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Permanently Delete</button>
                                </form>
                                {{--! active users buttons --}}
                                @else
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Delete </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endif
                    {{--! if no applications found --}}
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                No users found.
                            </td>
                        </tr>
    
                    @endforelse
                </tbody>
            </table>
            {{--! pagination links --}}
            <div class="my-4">
                {{ $users->links() }}
            </div>
        </div>
     </div>

</x-app-layout>
