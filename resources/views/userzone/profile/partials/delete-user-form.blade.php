<section class="flex flex-col items-start gap-6">
    <header>
        <h2 class="text-xl font-semibold text-ink">
            Delete Account
        </h2>

        <p class="mt-2 text-sm leading-relaxed text-muted">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <x-breeze.danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Delete Account</x-breeze.danger-button>

    <x-breeze.modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-xl font-semibold text-ink">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-2 text-sm leading-relaxed text-muted">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <x-breeze.input-label for="password" value="Password" class="sr-only" />

                <x-breeze.text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 block w-full"
                    placeholder="Password"
                />

                <x-breeze.input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <x-breeze.secondary-button x-on:click="$dispatch('close')">
                    Cancel
                </x-breeze.secondary-button>

                <x-breeze.danger-button>
                    Delete Account
                </x-breeze.danger-button>
            </div>
        </form>
    </x-breeze.modal>
</section>
