<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit - '.$jobVacancy->title) }}
        </h2>
    </x-slot>
    {{--! ============================== MAIN CONTENT ====================================== --}}
    <div class="overflow-x-auto p-6">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
            <form method="POST" action="{{ route('job-vacancy.update', ['job_vacancy'=>$jobVacancy->id,'toShow'=>request('toShow')]) }}" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="flex flex-row gap-6 justify-center">
                    {{--! job vacancy section --}}
                    <div class=" w-full bg-slate-50 p-6 rounded-lg shadow mb-6">
                        <h3 class="text-xl font-semibold ">Job Vacancy</h3>
                        <p class=" text-gray-600 text-sm mb-4">enter job vacancy details</p> 
                         {{--! title --}}             
                        <div class="mb-3">
                            <label for="title" class="block text-gray-700 font-bold mb-2">Title:</label>
                            <input type="text" value="{{ old('title',$jobVacancy->title) }}" name="title" id="title" placeholder="Enter job title" class=" {{ $errors->has('title') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('title')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! location --}}
                        <div class="mb-3">
                            <label for="location" class="block text-gray-700 font-bold mb-2">Location:</label>
                            <input type="text" value="{{ old('location',$jobVacancy->location) }}" name="location" id="location" placeholder="Enter company location" class=" {{ $errors->has('location') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('location')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror   
                        </div>
                        {{--! salary --}}
                        <div class="mb-3">
                            <label for="salary" class="block text-gray-700 font-bold mb-2">Expected salary (USD):</label>
                            <input type="number" value="{{ old('salary',$jobVacancy->salary) }}" name="salary" id="salary" placeholder="Enter expected salary" class=" {{ $errors->has('salary') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('salary')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! type --}}
                        <div class="mb-3">
                            <label for="type" class="block text-gray-700 font-bold mb-2">Job Type:</label>
                            <select class="w-full border border-gray-300 p-2 rounded" name="type" id="type">
                                <option value="">Select Type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" {{ old('type',$jobVacancy->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! companies options --}}  
                        {{--? display for admin only  --}}
                        @if (Auth::user()->role == 'admin')                       
                        <div class="mb-3">
                            <label for="company_id" class="block text-gray-700 font-bold mb-2">Company:</label>
                            <select class="w-full border border-gray-300 p-2 rounded" name="company_id" id="company_id">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id',$jobVacancy->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        {{--! categories options --}} 
                        <div class="mb-3">
                            <label for="category_id" class="block text-gray-700 font-bold mb-2">Job Category:</label>
                            <select class="w-full border border-gray-300 p-2 rounded" name="category_id" id="category_id">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id',$jobVacancy->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! Description --}} 
                        <div>
                            <label for="description" class="block text-gray-700 font-bold mb-2">Job Description:</label>
                            <textarea name="description" id="description" class="w-full border border-gray-300 p-2 rounded">{{ old('description',$jobVacancy->description) }}</textarea>
                            @error('description')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror   
                        </div>
                    </div>
                </div>
                {{--! form actions --}}
                <div class="flex items-center justify-between mt-4">
                    <a href="{{ route('job-vacancy.index') }}" class="text-blue-500 hover:underline">Back to Job vacancies</a>  
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-500">
                        Edit Job vacancy
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
