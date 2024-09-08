<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All My Appointments') }}
        </h2>
    </x-slot>
    @if (session()->has('message'))
    <div class="mt-2 bg-teal-500 text-sm text-white rounded-lg p-4" role="alert" tabindex="-1" aria-labelledby="hs-solid-color-success-label">
        <span id="hs-solid-color-success-label" class="font-bold">Success</span> {{session('message')}}.
    </div>
    @endif
    <livewire:all-appointments />
</x-app-layout>