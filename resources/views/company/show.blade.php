{{--! ============================== routes for roles ============================== --}}
@php
if(Auth::user()->role==='admin'){
    $routeParam=route('company.edit', ['company'=>$company->id,'toShow'=>'true']);
}elseif (Auth::user()->role==='recruiter') {
    $routeParam=route('my-company.edit');
}
@endphp

<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$company->name}}
            </h2>
        </x-slot>
        {{--! back button for admin only --}}
        @if (Auth::user()->role == 'admin')
        <div class="overflow-x-auto px-6 mt-3">
            <a  class="text-blue-600 hover:underline" href="{{ route('company.index')}}">← Back</a>
        </div>    
        @endif

        {{--! show notification  --}}
        <x-notification-message />

    {{--! ============================== MAIN CONTENT ====================================== --}}
        <div class="overflow-x-auto px-6 my-4">
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
            {{--!  Details Section --}}
            <h3 class="text-lg font-medium text-gray-900 mb-4">Company Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Owner</h4>
                    <p class="mt-1 text-gray-800">{{ $company->owner->name }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Email</h4>
                    <p class="mt-1 text-gray-800">{{ $company->owner->email }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Address</h4>
                    <p class="mt-1 text-gray-800">{{ $company->address }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Industry</h4>
                    <p class="mt-1 text-gray-800">{{ $company->industry }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Website</h4>
                    <p class="mt-1 text-blue-600 hover:underline">
                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                    </p>
                </div>
            </div>
            {{--! action buttons --}}
            <div class=" w-full mt-6 flex justify-end gap-x-1">
                <a href="{{$routeParam}}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                {{--? display delete button only for admin --}}
                @if (Auth::user()->role == 'admin')
                <form action="{{ route('company.destroy', $company->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Delete</button>
                </form>
                @endif
            </div>
            @if (Auth::user()->role == 'admin')

            {{--! tabs --}}
            <div class="mt-8">
                <nav class="flex space-x-4" >
                    <a href="{{ route('company.show', $company->id).'?tab=job-vacancies' }}" class="px-3 py-2 font-medium text-sm text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 {{ request()->get('tab') == 'job-vacancies' ? 'border-indigo-500' : 'border-transparent' }}" >
                        Job Vacancies
                    </a>
                    <a href="{{ route('company.show', $company->id).'?tab=job-applications' }}" class="px-3 py-2 font-medium text-sm text-gray-700 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 {{ request()->get('tab') == 'job-applications' ? 'border-indigo-500' : 'border-transparent' }}" >
                        Job Applications
                    </a>
                </nav>
            </div>
            {{--! job vacancies --}}
            <div class="mt-6">
                @if(request()->get('tab') == 'job-vacancies')
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Job Vacancies</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($company->jobVacancies as $vacancy)
                            <div class="bg-indigo-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800">{{ $vacancy->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $vacancy->description }}</p>
                                <p class="text-sm text-gray-600"><strong>Salary:</strong> {{ $vacancy->salary }}</p>
                                <div class="mt-4">
                                    <a href="{{route('job-vacancy.show', $vacancy->id)}}" class="text-blue-600 hover:underline ">View Details</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 ">No job vacancies found for this company.</p>
                        @endforelse
                    </div>
                @endif
            </div>
            {{--! job applications --}}
            <div class="mt-6">
                @if(request()->get('tab') == 'job-applications')
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Job Applications</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($company->applications as $application)
                            <div class="bg-indigo-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800">{{ $application->applicant_name }}</h4>
                                <p class="text-sm text-gray-600"><strong>Job Vacancy:</strong> {{ $application->jobVacancy->title }}</p>
                                <p class="text-sm text-gray-600"><strong>applicant name:</strong> {{ $application->applicant->name }}</p>
                                <p class="text-sm text-gray-600"><strong>Status:</strong> {{ $application->status }}</p>
                                <div class="mt-4">
                                    <a href="{{ route('job-vacancy.show', $application->jobVacancy->id) }}" class="text-blue-600 hover:underline ">View Details</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 ">No applications found for this company.</p>
                        @endforelse
                    </div>
                @endif
            </div>
            @endif
</x-app-layout>