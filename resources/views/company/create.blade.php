<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Company') }}
        </h2>
    </x-slot>

     <x-notification-message /> 
    {{--! ============================== MAIN CONTENT ====================================== --}}
    <div class="overflow-x-auto p-6">
        <div class="w-full mx-auto bg-white p-6 rounded-lg shadow">
            <form method="POST" action="{{ route('company.store') }}" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-row gap-6 justify-center"> 
                    {{--! company section --}}
                    <div class=" w-full bg-slate-50 p-6 rounded-lg shadow mb-6">
                        <h3 class="text-xl font-semibold ">Company</h3>
                        <p class=" text-gray-600 text-sm mb-4">enter company Information details</p>
                        {{--! name --}}
                        <div class="mb-3">
                            <label for="name" class="block text-gray-700 font-bold mb-2">Company Name:</label>
                            <input type="text" value="{{ old('name') }}" name="name" id="name" placeholder="Enter company name" class=" {{ $errors->has('name') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('name')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! email --}}
                        <div class="mb-3">
                            <label for="email" class="block text-gray-700 font-bold mb-2">Company email:</label>
                            <input type="email" value="{{ old('email') }}" name="email" id="email" placeholder="Enter company email" class=" {{ $errors->has('email') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('email')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror   
                        </div>
                        {{--! adress --}}
                        <div class="mb-3">
                            <label for="address" class="block text-gray-700 font-bold mb-2">Address:</label>
                            <input type="text" value="{{ old('address') }}" name="address" id="address" placeholder="Enter company address" class=" {{ $errors->has('address') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('address')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! industries options --}}
                        <div class="mb-3">
                            <label for="industry" class="block text-gray-700 font-bold mb-2">Industry:</label>
                            <select class="w-full border border-gray-300 p-2 rounded" name="industry" id="industry">
                                <option value="">Select Industry</option>
                                @foreach ($industries as $industry)
                                    <option value="{{ $industry }}" {{ old('industry') == $industry ? 'selected' : '' }}>{{ $industry }}</option>
                                @endforeach
                            </select>
                            @error('industry')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! website --}}
                        <div>
                            <label for="website" class="block text-gray-700 font-bold mb-2">Website (optional):</label>
                            <input type="text" value="{{ old('website') }}" name="website" id="website" placeholder="Enter company website" class=" {{ $errors->has('website') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('website')
                            <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror   
                        </div>
                        {{--! logo --}}
                        {{--? define the file name and error variables --}}
                        <div class="mt-3"  x-data="{fileName:'', preview: null , hasError:{{ $errors->has('logo')?'true':'false' }} }">
                        <label for="logo" class="block text-gray-700 font-bold mb-2">Upload logo:</label>
                        {{--! upload  --}}
                        <div class="flex items-center">
                            <div class="flex-1">
                                <label for="new_logo_file" class="block text-white cursor-pointer ">                                    
                                    {{--! border style --}}
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 hover:border-blue-500 transition"
                                         :class="{'border-blue-500':fileName, 'border-red-500':hasError}">
                                         {{--! input--}}
                                         <input @change="
                                                let file = $event.target.files[0];
                                                if(file){
                                                    fileName = file.name;
                                                    preview = URL.createObjectURL(file);
                                                }
                                            " 
                                            type="file"name="logo" id="new_logo_file" class="hidden" accept=".jpg,.jpeg,.png">
                                          {{--! inside text--}}
                                          <div class="text-center">

                                            {{--? template -> to show one of them --}}
                                            {{--? if file not uploaded--}}
                                            <template x-if="!preview">
                                                <p class="text-gray-400">Click to upload logo (Max 1MB)</p>
                                            </template>
                                            
                                            {{--? if file uploaded--}}
                                            <template x-if="preview">
                                                <div>
                                                    <img :src="preview" alt="Logo Preview" class="mx-auto mb-2 h-20">
                                                    <p x-text="fileName" class="mt-2 text-blue-400 "></p>
                                                    <p class="text-gray-400 text-sm mt-1 ">Click to change logo</p>
                                                </div>
                                            </template>
                                          </div>
                                    </div>
                                </label>
                                 <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                            </div>
                        </div>
                        </div>
                       
                    </div>
                    {{--! owner section --}}
                    <div class=" w-full bg-slate-50 p-6 rounded-lg shadow mb-6">
                        <h3 class="text-xl font-semibold">Owner</h3>
                        <p class=" text-gray-600 text-sm mb-4">enter owner Information details</p>
                        {{--! name --}}
                        <div class="mb-4">
                            <label for="owner_name" class="block text-gray-700 font-bold mb-2"> Owner Name:</label>
                            <input type="text" value="{{ old('owner_name') }}" name="owner_name" id="owner_name" placeholder="Enter owner name" class=" {{ $errors->has('owner_name') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('owner_name')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! email --}}
                        <div>
                            <label for="owner_email" class="block text-gray-700 font-bold mb-2"> Owner Email:</label>
                            <input type="email" value="{{ old('owner_email') }}" name="owner_email" id="owner_email" placeholder="Enter owner email" class=" {{ $errors->has('owner_email') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" >
                            @error('owner_email')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--! password --}}
                        <div class="mt-4 relative" x-data="{ show: false }">
                            <x-input-label for="password" :value="__('Password')" />                        
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="owner_password" id="owner_password" placeholder="Enter owner password" class=" {{ $errors->has('owner_password') ? 'border-red-500' : '' }} w-full border border-gray-300 p-2 rounded placeholder-gray-400" />
                                {{--! Eye Icon for Show/Hide Password --}}
                                <button type="button" class="absolute inset-y-0 right-2 flex items-center text-gray-500"
                                    @click="show = !show">
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825a9.56 9.56 0 01-1.875.175c-4.478 0-8.268-2.943-9.542-7 1.002-3.364 3.843-6 7.542-7.575M15 12a3 3 0 00-6 0 3 3 0 006 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('owner_password')
                                <div class="text-red-500 text-sm my-2">{{ $message }}</div>
                            @enderror
                        </div>                                                
                    </div>
                </div>
                {{--! form actions --}}
                <div class="flex items-center justify-between mt-4">
                    <a href="{{ route('company.index') }}" class="text-blue-500 hover:underline">Back to companies</a>  
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-500">
                        Create company
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
