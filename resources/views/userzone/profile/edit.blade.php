<x-base-layout title="Profile">
    <h1 class="mb-8 text-3xl font-semibold leading-tight tracking-tight text-ink">Profile</h1>

    <div class="flex max-w-2xl flex-col gap-6">
        <div class="rounded-2xl bg-surface p-6 sm:p-8">
            @include('userzone.profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl bg-surface p-6 sm:p-8">
            @include('userzone.profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl bg-soft/60 p-6 sm:p-8">
            @include('userzone.profile.partials.delete-user-form')
        </div>
    </div>
</x-base-layout>
