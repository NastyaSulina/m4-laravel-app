<x-guest-layout>
    <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Check your email</h1>
    <div class="mb-6 text-sm leading-relaxed text-muted">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 rounded-xl bg-success-soft px-4 py-3 text-sm leading-relaxed text-success">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
        <form class="flex flex-col gap-5" method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-breeze.primary-button>
                    Resend Verification Email
                </x-breeze.primary-button>
            </div>
        </form>

        <form class="flex flex-col gap-5" method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-sm text-sm font-medium text-ink underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
