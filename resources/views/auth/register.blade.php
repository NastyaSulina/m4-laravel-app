<x-guest-layout>
    <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Create your account</h1>
    <form class="flex flex-col gap-5" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-breeze.input-label for="name" value="Name" />
            <x-breeze.text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-breeze.input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-breeze.input-label for="email" value="Email" />
            <x-breeze.text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-breeze.input-label for="password" value="Password" />

            <x-breeze.text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-breeze.input-label for="password_confirmation" value="Confirm Password" />

            <x-breeze.text-input id="password_confirmation" class="mt-2 block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-breeze.input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
            <a class="rounded-sm text-sm font-medium text-ink underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink" href="{{ route('login') }}">
                Already registered?
            </a>

            <x-breeze.primary-button>
                Register
            </x-breeze.primary-button>
        </div>
    </form>
</x-guest-layout>
