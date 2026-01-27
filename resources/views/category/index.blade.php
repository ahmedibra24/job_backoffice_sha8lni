<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{--? check if archived or not to display the correct title --}}
            @if (request()->has('archived'))
                {{ __('Archived Categories') }}      
            @else
                {{ __('Job Categories') }}     
            @endif
        </h2>
    </x-slot>
    {{--! ============================== ACTION BUTTONS =============================== --}}
    <div class="mr-6 mt-6 mb-3 flex items-center justify-end gap-x-3">

        {{--? check if archived or not to display the correct button --}}
        @if (request()->has('archived'))      
        <a href="{{ route('category.index') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
             Active categories
        </a>
        @else
        <a href="{{ route('category.index') }}?archived=true" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
             Archived
        </a>
        <a href="{{ route('category.create') }}"
            class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Add New Category
        </a>
        @endif
    </div>

    {{--! show notification  --}}
    <x-notification-message />

    {{--! ============================== TABLE CONTENT ====================================== --}}
    <div class="overflow-x-auto px-6">
        {{--! table header --}}
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            {{--! table body --}}
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ( $categories as $category )
                
                <tr class="hover:bg-indigo-50">
                    {{--! category name --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $category->name }}</div>
                    </td>
                    {{--! actions buttons --}}
                    <td class="px-6 py-4 whitespace-nowrap  text-sm font-medium">
                        {{--! archived category buttons --}}
                        @if (request()->has('archived'))
                        <form action="{{ route('category.restore', $category->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-green-600 hover:text-green-900"> 🔄 Restore</button>
                        </form>
                        <form action="{{ route('category.destroy', $category->id) }}?archived=true" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure you want to delete this category permanently?')" type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Permanently Delete</button>
                        </form>
                        {{--! active category buttons --}}
                        @else
                        <a href="{{ route('category.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900">✍🏻 Edit</a>
                        <form action="{{ route('category.destroy', $category->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ml-2 text-red-600 hover:text-red-900">🗑️ Delete </button>
                        </form>
                        @endif
                    </td>
                </tr>
                {{--! if no categories found --}}
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        No categories found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{--! pagination links --}}
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>

</x-app-layout>
