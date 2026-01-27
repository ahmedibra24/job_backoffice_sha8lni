<x-app-layout>
        {{--! ============================== HEADER ====================================== --}}
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$jobVacancy->title}}
            </h2>
        </x-slot>

        {{--! back button  --}}
        <div class="overflow-x-auto px-6 mt-3">
            <a  class="text-blue-600 hover:underline" href="{{ route('job-vacancy.index')}}">← Back</a>
        </div>

        {{--! show notification  --}}
        <x-notification-message />

        {{--! ============================== MAIN CONTENT ====================================== --}}
        <div class="overflow-x-auto px-6 my-4">
            <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
                {{--!  Details Section --}}
                <h3 class="text-lg font-medium text-gray-900 mb-4">Job Vacancy Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-bold text-gray-950">Company</h4>
                        <p class="mt-1 text-gray-800">{{ $jobVacancy->company->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-950">Location</h4>
                        <p class="mt-1 text-gray-800">{{ $jobVacancy->location }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-950">Type</h4>
                        <p class="mt-1 text-gray-800">{{ $jobVacancy->type }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-950">Salary</h4>
                        <p class="mt-1 text-gray-800">${{ number_format($jobVacancy->salary,2) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-sm font-bold text-gray-950">Description</h4>
                    <p class="mt-1 text-gray-800">{{ $jobVacancy->description }}</p>
                </div>
                {{--! action buttons --}}
                <div class=" w-full mt-6 flex justify-end gap-x-1">
                    <a href="{{ route('job-vacancy.edit', ['job_vacancy'=>$jobVacancy->id,'toShow'=>'true']) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                    <form action="{{ route('job-vacancy.destroy', $jobVacancy->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Delete</button>
                    </form>
                </div>
                {{--! tabs --}}
                <div class="mt-8">
                    <nav class="flex space-x-4" >
                        <a href="{{ route('job-vacancy.show', $jobVacancy->id)}}" class="px-3 py-2 font-medium text-sm text-gray-700 hover:text-gray-900 border-b-2 border-indigo-500 hover:border-gray-300" >
                            Job Applications
                        </a>
                    </nav>
                </div>
                {{--! job applications --}}
                <div class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($jobVacancy->jobApplications as $application)
                            <div class="bg-indigo-50 p-4 rounded-lg shadow-sm">
                                <p class=" mb-1 text-sm text-gray-600"><strong>applicant name:</strong> {{ $application->applicant->name }}</p>
                                <p class=" mb-1 text-sm text-gray-600"><strong>Status:</strong> {{ $application->status }}</p>
                                <p class=" mb-1 text-sm text-gray-600"><strong>Score:</strong> {{ $application->aiGeneratedScore }}</p>
                                <p class=" mb-1 text-sm text-gray-600"><strong>feedback:</strong> {{ $application->aiGeneratedFeedback }}</p>

                                <div class="mt-4">
                                    <a href="{{ route('application.show', $application->id) }}" class="text-blue-600 hover:underline ">View Details</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 ">No applications found for this job vacancy.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>