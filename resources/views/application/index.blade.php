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
    
    {{--! ============================== ACTION BUTTONS ====================================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">

        {{--? check if archived or not to display the correct button --}}
        @if (request()->has('archived'))      
        <a href="{{ route('application.index') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Active Job Applications
        </a>
        @else
        
        <a href="{{ route('application.index') }}?archived=true" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Archived
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
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $application->jobVacancy?->company->name??'N/A' }}</div>
                    </td>
                    @endif 
                    {{--! status --}}                  
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class=" font-semibold text-sm text-center text-gray-900 p-1 {{ $application->status == 'pending' ? 'bg-yellow-500  text-white rounded-lg' : ($application->status == 'accepted' ? 'bg-green-500  text-white rounded-lg' : 'bg-red-500  text-white rounded-lg') }}">{{ $application->status }}</div>
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
</x-app-layout>
