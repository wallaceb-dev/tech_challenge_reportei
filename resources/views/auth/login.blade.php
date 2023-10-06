<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center">
        <a href="{{ route('github.login') }}">
            <img src="https://icon-library.com/images/github-icon-white/github-icon-white-6.jpg" alt="Github Icon"
                width="60px" class="mx-auto scale-100 hover:scale-125 ease-in duration-200" title="Sign up with Github">
                <span class="text-lg text-white">Sign up</span>
        </a>
    </div>

</x-guest-layout>
