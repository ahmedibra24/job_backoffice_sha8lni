<x-app-layout>
        {{--! ============================== HEADER ====================================== --}}
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$application->applicant->name.'| applied to '.$application->jobVacancy?->title ?? 'N/A'}}
            </h2>
        </x-slot>
        {{--! ============================== BACK BUTTON ====================================== --}}

        <div class="overflow-x-auto px-6 mt-3">
            <a  class="text-blue-600 hover:underline" href="{{ route('application.index')}}">← Back</a>
        </div>
        {{--! show notification  --}}
        <x-notification-message />
        {{--! ============================== APPLICATION DETAILS ====================================== --}}
        <div class="overflow-x-auto px-6 my-4">
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
            {{--!  Details Section --}}
            <h3 class="text-lg font-medium text-gray-900 mb-4">Application Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Applicant</h4>
                    <p class="mt-1 text-gray-800">{{ $application->applicant?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Job Vacancy</h4>
                    <p class="mt-1 text-gray-800">{{ $application->jobVacancy?->title  ?? 'N/A'}}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Company</h4>
                    <p class="mt-1 text-gray-800">{{ $application->jobVacancy?->company->name  ?? 'N/A'}}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950">Status</h4>
                    <p class=" mt-1 font-semibold text-sm text-center max-w-20 text-gray-900 p-1 {{ $application->status == 'pending' ? 'bg-yellow-500  text-white rounded-lg' : ($application->status == 'accepted' ? 'bg-green-500  text-white rounded-lg' : 'bg-red-500  text-white rounded-lg') }}">{{ $application->status }}</p>
                </div>
                <div>
                    <a href="{{ $application->resume->fileUri }}" target="_blank" class="mt-1 text-blue-600 hover:underline">
                        Download Resume
                    </a>
                </div>
            </div>
            {{--! action buttons --}}
            <div class=" w-full mt-6 flex justify-end gap-x-1">
                {{--? pass toShow query param to edit page to redirect to show page after edit --}}
                <a href="{{ route('application.edit', ['application'=>$application->id,'toShow'=>'true']) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                <form action="{{ route('application.destroy', $application->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Delete</button>
                </form>
            </div>
            {{--! tabs --}}
            <div class="mt-8">
                <nav class="flex space-x-4" >
                    <a href="{{ route('application.show', $application->id).'?tab=resume' }}" class="px-3 py-2 font-medium text-sm text-gray-700 hover:text-gray-900 border-b-2  hover:border-gray-300 {{ request()->get('tab') == 'resume' ? 'border-indigo-500' : 'border-transparent' }}" >
                        Resume Details
                    </a>
                    <a href="{{ route('application.show', $application->id).'?tab=aiFeedback' }}" class="px-3 py-2 font-medium text-sm text-gray-700 hover:text-gray-900 border-b-2  hover:border-gray-300 {{ request()->get('tab') == 'aiFeedback' ? 'border-indigo-500' : 'border-transparent' }}" >
                        Ai Feedback
                    </a>
                </nav>
            </div>
            {{--! Resume Details --}}
            <div class="mt-6">
                @if(request()->get('tab') == 'resume')
                        <div class="bg-indigo-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600"><strong>Summary:</strong></p>
                            <p class=" mb-4 text-sm text-gray-600">{{ $application->resume->summary }}</p>
                            <p class=" text-sm text-gray-600"><strong>Skills:</strong></p>
                            <p class=" mb-4 text-sm text-gray-600">{{ $application->resume->skills }}</p>
                            <p class=" text-sm text-gray-600"><strong>Experience:</strong></p>
                            <p class=" mb-4 text-sm text-gray-600">{{ $application->resume->experience }}</p>
                            <p class=" text-sm text-gray-600"><strong>Education:</strong></p>
                            <p class=" mb-4 text-sm text-gray-600">{{ $application->resume->education }}</p>
                        </div>
                @endif
            </div>
            {{--! Ai Feedback --}}
            <div class="mt-6 ">
                @if(request()->get('tab') == 'aiFeedback')
                        <div class="bg-indigo-50 p-4 rounded-lg shadow-sm w-full">
                            <p class="text-sm text-gray-600"><strong>Score:</strong> {{ $application->aiGeneratedScore }}</p>
                            <p class="text-sm text-gray-600"><strong>Feedback:</strong> {{ $application->aiGeneratedFeedback }}</p>
                        </div>
                @endif
        </div>
</x-app-layout>