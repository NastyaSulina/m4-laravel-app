<x-guest-layout>
    <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Forgot your password?</h1>
    <div class="mb-6 text-sm leading-relaxed text-muted">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <!-- Session Status -->
    <x-breeze.auth-session-status class="mb-6" :status="session('status')" />

    <form class="flex flex-col gap-5" method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-breeze.input-label for="email" value="Email" />
            <x-breeze.text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
            <x-breeze.primary-button>
                Email Password Reset Link
            </x-breeze.primary-button>
        </div>
    </form>
</x-guest-layout>
