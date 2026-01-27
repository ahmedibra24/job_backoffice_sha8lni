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
    {{--! ============================== ACTION BUTTONS ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">
        {{--? check if archived or not to display the correct button --}}
        @if (request()->has('archived'))      
        <a href="{{ route('job-vacancy.index') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
             Active Job Vacancies
        </a>
        @else
        <a href="{{ route('job-vacancy.index') }}?archived=true" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Archived
        </a>
        <a href="{{ route('job-vacancy.create') }}"
            class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Add New job vacancy
        </a>
        @endif
    </div>

    {{--! show notification  --}}
    <x-notification-message />
    <div class="overflow-x-auto px-6">

    {{--! ============================== TABLE CONTENT ====================================== --}}
        <table class="min-w-full divide-y divide-gray-200">
            {{--! table header --}}
            <thead class="bg-indigo-50">
                <tr>
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
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
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

</x-app-layout>
