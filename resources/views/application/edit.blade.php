<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Status - '. $application->applicant->name) }}
        </h2>
    </x-slot>

    {{--! ============================== MAIN CONTENT ====================================== --}}
    <div class="overflow-x-auto p-6">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
            {{--? receive toShow query param from request --}}
            {{--? then pass toShow query param to update controller to redirect to show page after edit --}}
            <form method="POST" action="{{ route('application.update', ['application'=>$application->id,'toShow'=>request('toShow')]) }}" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="flex flex-row gap-6 justify-center">
                    {{--! display application data and edit status only --}}
                    {{--! application data --}}
                    <div class=" w-full bg-slate-50 p-6 rounded-lg shadow mb-6">
                        <h3 class="text-xl font-semibold ">Application Information</h3>                      
                        <div class="mb-3">
                            <label class="block text-gray-700 font-bold ">Applicant Name:</label>
                            <p class="text-gray-900">{{ $application->applicant->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-700 font-bold ">Job Vacancy:</label>
                            <p class="text-gray-900">{{ $application->jobVacancy->title }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-700 font-bold ">Company:</label>
                            <p class="text-gray-900">{{ $application->jobVacancy->company->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-700 font-bold ">Ai score:</label>
                            <p class="text-gray-900">{{ $application->aiGeneratedScore }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-700 font-bold ">Ai feedback:</label>
                            <p class="text-gray-900">{{ $application->aiGeneratedFeedback }}</p>
                        </div>                   
                    {{--! edit status --}}
                        <div class="mb-3">
                            <label for="status" class="block text-gray-700 font-bold mb-2">status:</label>
                            <select class="w-full border border-gray-300 p-2 rounded" name="status" id="status">
                                @foreach ($status as $statusOption)
                                    <option value="{{ $statusOption }}" {{ old('status',$application->status) == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{--! form actions --}}
                <div class="flex items-center justify-between mt-4">
                    <a href="{{ route('application.index') }}" class="text-blue-500 hover:underline">Back to Applications</a>  
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-500">
                        Edit status
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
