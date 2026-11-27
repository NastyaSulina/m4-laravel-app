<x-guest-layout>
    <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Welcome back</h1>
    <!-- Session Status -->
    <x-breeze.auth-session-status class="mb-6" :status="session('status')" />

    <form class="flex flex-col gap-5" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-breeze.input-label for="email" value="Email" />
            <x-breeze.text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-breeze.input-label for="password" value="Password" />

            <x-breeze.text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5">
                <input id="remember_me" type="checkbox" class="size-4 cursor-pointer rounded border-line text-ink focus:ring-ink/20" name="remember">
                <span class="text-sm text-muted">Remember me</span>
            </label>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
            @if (Route::has('password.request'))
                <a class="rounded-sm text-sm font-medium text-ink underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif

            <x-breeze.primary-button>
                Log in
            </x-breeze.primary-button>
        </div>
    </form>
</x-guest-layout>
