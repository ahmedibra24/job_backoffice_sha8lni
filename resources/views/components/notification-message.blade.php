    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave.duration.600ms x-init="setTimeout(() => show = false, 5000)" class="my-4 p-6 mx-auto max-w-7xl text-center text-sm overflow-hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @elseif (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave.duration.600ms x-init="setTimeout(() => show = false, 5000)" class="my-4 p-6 mx-auto max-w-7xl text-center text-sm overflow-hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    
