{{-- delete checkboxes selected --}}
@props(['indexRoute' => null , 'addRoute' => null])

<div class="w-full flex items-center justify-end gap-x-3 " >
    {{--? check if archived or not to display the correct button --}}
    @if (request()->has('archived'))      
    <a href="{{ $indexRoute }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
         Active
    </a>
    @else
    <a href="{{ $indexRoute }}?archived=true" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Archived
    </a>
        @if ($addRoute) 
        <a href="{{ $addRoute }}"
            class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Add New
        </a>
        @endif
    @endif
</div>
