<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @if (isset($repo))
                {{ $repo->name }}
            @else
                The repository hasn't been found!
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="text-red-500 mb-12 bg-red-200 p-6 rounded-sm">{{ $errors->first() }}</div>
            @endif
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="flex justify-between items-center mb-12">
                <a class="text-white flex items-center hover:underline" href="{{ route('repos.index') }}">
                    <x-icons.chevron-left /> Voltar
                </a>
                <div class="flex text-white">
                    <form method="get" class="flex items-center text-white">
                        <p class="pr-3">From <b class="underline">today</b> back to </p>
                        @include('components.repo-daterange-picker')
                        <button type="submit" class="ml-3 p-2 bg-sky-600 rounded-lg active:bg-sky-700"
                            onclick="this.form.submit()">Search</button>
                    </form>
                </div>
            </div>

            <div id="app" class="bg-white/[.11] p-8 rounded-md">
                {!! $chart->container() !!}
            </div>
        </div>
    </div>
    {!! $chart->script() !!}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/datepicker.min.js"></script>
</x-app-layout>
