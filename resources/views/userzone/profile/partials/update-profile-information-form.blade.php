<section>
    <header>
        <h2 class="text-xl font-semibold text-ink">
            Profile Information
        </h2>

        <p class="mt-2 text-sm leading-relaxed text-muted">
            Update your account's profile information and email address.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 flex flex-col gap-5">
        @csrf
        @method('patch')

        <div>
            <x-breeze.input-label for="name" value="Name" />
            <x-breeze.text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-breeze.input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-breeze.input-label for="email" value="Email" />
            <x-breeze.text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-breeze.input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-3 rounded-xl bg-soft p-3 text-sm leading-relaxed text-ink">
                        Your email address is unverified.

                        <button form="send-verification" class="rounded-sm text-sm font-medium text-ink underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">
                            Click here to re-send the verification email.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-3 rounded-xl bg-success-soft px-4 py-3 text-sm leading-relaxed text-success">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-breeze.primary-button>Save</x-breeze.primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="rounded-full bg-success-soft px-3 py-1 text-sm text-success"
                >Saved.</p>
            @endif
        </div>
    </form>
</section>
