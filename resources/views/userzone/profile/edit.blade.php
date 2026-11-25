<x-base-layout title="Profile">
    <h1 class="mb-6 text-2xl font-semibold text-slate-900">Profile</h1>

    <div class="space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
            <div class="max-w-xl">
                @include('userzone.profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
            <div class="max-w-xl">
                @include('userzone.profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
            <div class="max-w-xl">
                @include('userzone.profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-base-layout>
