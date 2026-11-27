<x-guest-layout>
    <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Confirm your password</h1>
    <div class="mb-6 text-sm leading-relaxed text-muted">
        This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form class="flex flex-col gap-5" method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-breeze.input-label for="password" value="Password" />

            <x-breeze.text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-1">
            <x-breeze.primary-button>
                Confirm
            </x-breeze.primary-button>
        </div>
    </form>
</x-guest-layout>
