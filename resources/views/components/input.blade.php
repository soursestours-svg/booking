@props([
    'label' => null,
    'error' => null,
    'required' => false,
])

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input {{ $attributes->merge([
        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm ' .
                  ($error ? 'border-red-500 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')
    ]) }}>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
