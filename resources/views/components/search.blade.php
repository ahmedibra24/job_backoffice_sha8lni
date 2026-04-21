{{-- delete checkboxes selected --}}
@props(['route' => ''  ,'placeholder' => ''])

<div class="ml-6  flex w-full items-center justify-start  gap-x-3">
    <form action="{{ route($route) }}" method="GET" class="flex ">
        <div class="flex items-stretch overflow-hidden rounded-xl ring-1 ring-gray-300 bg-white">
            <input name="search" value="{{ request('search') }}" type="text" placeholder="{{ $placeholder }}" class=" h-10 w-80 bg-transparent px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none rounded-l-xl">
            <button type="submit" class="px-5 text-white font-medium bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500">
                Search
            </button>
            {{--? to keep the archived filter with search --}}
            @if (request()->has('archived'))
            <input type="hidden" name="archived" value="true">                            
            @endif
        </div>
        {{--? to keep the filter with search --}}
        @if (request()->has('filter'))
            <input type="hidden" name="filter" value="{{ request('filter') }}">                            
        @endif

        {{--! clear search --}}
        @if (request()->has('search'))
            <a href="{{route($route,['archived'=>request('archived') ? 'true' : null , 'filter'=>request('filter')])}}"
             class="px-4 py-2  hover:cursor-pointer hover:border-spacing-1">
             clear
            </a>
        @endif
    </form>
</div>
