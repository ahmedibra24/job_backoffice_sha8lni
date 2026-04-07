@props(['route' => null])


<form action="{{ $route }}?archived=true" method="POST">
    @csrf
    @method('PUT')

    <template x-for="id in items" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>

    <button onclick="return confirm('Are you sure you want to restore these selected companies?')" type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
        Restore Selected <span x-text="items.length"></span>
    </button>
</form>
