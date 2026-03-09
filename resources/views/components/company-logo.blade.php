@props(['logoUri', 'logoName','companyName'])

@if ($logoUri)
    <img src="{{ Storage::disk('cloud')->url($logoUri) }}" alt="{{ $logoName }}" {{ $attributes->merge(['class' => 'w-10 h-10 rounded-full mr-2 object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => 'w-10 h-10 rounded-full mr-2 bg-gray-300 flex items-center justify-center text-gray-600']) }}>
        <span class="text-sm font-medium">{{ strtoupper(substr($companyName, 0, 2)) }}</span>
    </div>
@endif 

