<x-guest-layout>
    <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Reset your password</h1>
    <form class="flex flex-col gap-5" method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-breeze.input-label for="email" value="Email" />
            <x-breeze.text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-breeze.input-label for="password" value="Password" />
            <x-breeze.text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
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
            <x-breeze.primary-button>
                Reset Password
            </x-breeze.primary-button>
        </div>
    </form>
</x-guest-layout>
