{{-- delete checkboxes selected --}}

@props(['route' => null])

<form action="{{ $route }}?archived=true" method="POST">
    @csrf
    @method('DELETE')

    <template x-for="id in items" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>

    <button onclick="return confirm('Are you sure you want to delete these selected companies permanently?')" type="submit" class=" px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
        Permanently Delete Selected <span x-text="items.length"></span>
    </button>
</form>
