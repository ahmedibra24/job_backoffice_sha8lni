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
    {{--! ============================== ACTION BUTTONS ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--? check if archived or not to display the correct button --}}
        @if (request()->has('archived'))      
        <a href="{{ route('company.index') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
             Active Companies
        </a>
        @else
        <a href="{{ route('company.index') }}?archived=true" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Archived
        </a>
        <a href="{{ route('company.create') }}"
            class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Add New company
        </a>
        @endif
    </div>

    {{--! show notification  --}}
    <x-notification-message />
    {{--! ============================== TABLE CONTENT ====================================== --}}
    <div class="overflow-x-auto px-6">
        <table class="min-w-full divide-y divide-gray-200">
            {{--! table header --}}
            <thead class="bg-indigo-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Company name
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
                    {{--! name --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{--? to display the name as link in index and without link if archived --}}
                            @if (request()->has('archived'))
                            <span class="text-gray-500">{{ $Company->name }}</span>
                            @else
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
                            <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
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
                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
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
</x-app-layout>
