<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Repositories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="text-red-500 mb-12 bg-red-200 p-6 rounded-sm">{{ $errors->first() }}</div>
            @endif
            <x-auth-session-status class="mb-4" :status="session('status')" />
            @if (empty($repositories->count()))
                <p class="text-lg text-slate-300 text-center">
                    User {{ Auth::user()->name }} has no repositories yet.
                </p>
            @else
                <div class="flex justify-between items-center">
                    <h3 class="text-xl text-white">{{ $repositories->first()->owner }}</h3>
                    @include('components.repository-search')
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100 grid md:grid-cols-4 sm:grid-cols-2 gap-5">
                    @foreach ($repositories as $repository)
                        <a href="{{ route('repos.show', ['owner' => $repository->owner, 'repo' => $repository->name]) }}"
                            class="flex justify-center items-center bg-gradient-to-r from-slate-700 border border-slate-400 to-slate-900 rounded hover:scale-125 ease-in text-center duration-75 py-6">
                            <span>{{ $repository->name }}</span>
                            <x-icons.chevron-right />
                        </a>
                    @endforeach
                </div>
                {!! $repositories->links() !!}
            @endif
        </div>
    </div>
</x-app-layout>
