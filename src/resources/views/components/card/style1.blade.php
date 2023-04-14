<div class="bg-white rounded-md flex shadow-lg w-full overflow-hidden">
    <span {{ $attributes->merge(['class' => 'p-3 flex items-center']) }}>
        {{ $icon }}
    </span>
    <div class="p-3">
        <p class="font-medium">
            {{ $slot }}
        </p>
        <small class="text-gray-400">{{ $count ?? '' }}</small>
    </div>
</div>
