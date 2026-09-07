@props([
    'title' => null,
    'pageTitle' => null,
    'pageSubtitle' => null,
    'breadcrumbs' => [],
])

<x-layouts.admin 
    :title="$title"
    :pageTitle="$pageTitle"
    :pageSubtitle="$pageSubtitle"
    :breadcrumbs="$breadcrumbs"
    {{ $attributes }}
>
    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset

    {{ $slot }}
</x-layouts.admin>
