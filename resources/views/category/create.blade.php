<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Category') }}
        </h2>
    </x-slot>
    {{--! ============================== MAIN CONTENT ====================================== --}}
    <div class="overflow-x-auto p-6">
        <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
            <form method="POST" action="{{ route('category.store') }}">
                @csrf               
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Category Name:</label>
                    <input type="text" value="{{ old('name') }}" name="name" id="name" placeholder="Enter category name" class=" {{ $errors->has('name') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                    @error('name')
                        <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                    @enderror
                </div>
                {{--! form actions --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('category.index') }}" class="text-blue-500 hover:underline">Back to Categories</a>  
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-500">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
