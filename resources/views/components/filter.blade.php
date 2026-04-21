@props(['route', 'options'])

<div class="w-full ml-6  flex items-center gap-3 self-start md:self-auto">
    {{--! search --}}
    @foreach ($options as $option)
        <a href="{{ route($route, ['filter' => $option , 'search'=>request('search'), 'archived'=>request('archived') ? 'true' : null]) }}"
            class="px-4 py-2 rounded-xl ring-1 hover:cursor-pointer 
            {{ request('filter') == $option ? 'px-5 text-white font-medium bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500' : 'bg-indigo-500/20 hover:bg-indigo-500/30 text-gray-900 ring-indigo-400/30' }}">
            {{ ucfirst($option) }}
        </a>
    @endforeach 

    {{--! clear filter --}}
    @if (request()->has('filter'))
        <a href="{{ route($route ,['archived'=>request('archived') ? 'true' : null ,'search'=>request('search')]) }}" class="text-sm text-blue-500 hover:text-blue-700">
            Clear filter
        </a>
    @endif
</div>

