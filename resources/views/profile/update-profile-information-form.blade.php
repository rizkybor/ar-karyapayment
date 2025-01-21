<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden" wire:model.live="photo" x-ref="photo"
                    x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                        class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model.live="state.name" required
                autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model.live="state.email" required
                autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) &&
                    !$this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 dark:focus:ring-offset-gray-800"
                        wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-700">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- NIP -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="nip" value="{{ __('NIP') }}" />
            <x-input id="nip" type="number" class="mt-1 block w-full" wire:model.live="state.nip"
                autocomplete="nip" />
            <x-input-error for="nip" class="mt-2" />
        </div>

        <!-- Department -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="department" value="{{ __('Department') }}" />
            <x-input id="department" type="text" class="mt-1 block w-full" wire:model.live="state.department"
                autocomplete="department" />
            <x-input-error for="department" class="mt-2" />
        </div>

        <!-- Position -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="position" value="{{ __('Position') }}" />
            <x-input id="position" type="text" class="mt-1 block w-full" wire:model.live="state.position"
                autocomplete="position" />
            <x-input-error for="position" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="col-span-6 sm:col-span-4">
            <!-- Label -->
            <x-label for="role" value="{{ __('Role') }}" />

            <!-- Dropdown Select -->
            <select id="role" class="mt-1 block w-full form-select " wire:model.live="state.role">
                <option value="">Pilih Role</option>
                <option value="maker">Maker</option>
                <option value="kadiv">Kadiv</option>
                <option value="bendahara">Bendahara</option>
                <option value="manager_anggaran">Manager Anggaran</option>
                <option value="direktur_keuangan">Direktur Keuangan</option>
                <option value="pajak">Pajak</option>
            </select>

            <!-- Error Message -->
            <x-input-error for="role" class="mt-2" />
        </div>

        <!-- Employee Status -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="employee_status" value="{{ __('Employee Status') }}" />
            <x-input id="employee_status" type="text" class="mt-1 block w-full"
                wire:model.live="state.employee_status" autocomplete="employee_status" />
            <x-input-error for="employee_status" class="mt-2" />
        </div>

        <!-- Gender -->
        <div class="col-span-6 sm:col-span-4">
            <!-- Label -->
            <x-label for="gender" value="{{ __('Gender') }}" />

            <!-- Radio Button Group -->
            {{-- Pria --}}
            <div class="mt-1 space-y-2">
                <label class="inline-flex items-center">
                    <input type="radio" id="gender_pria" value="pria"
                        class="form-radio h-4 w-4 transition duration-150 ease-in-out"
                        wire:model.live="state.gender" />
                    <span class="ml-2">Pria</span>
                </label>
                {{-- Wanita --}}
                <label class="inline-flex items-center">
                    <input type="radio" id="gender_wanita" value="wanita"
                        class="form-radio h-4 w-4 transition duration-150 ease-in-out"
                        wire:model.live="state.gender" />
                    <span class="ml-2">Wanita</span>
                </label>
            </div>

            <!-- Error Message -->
            <x-input-error for="gender" class="mt-2" />
        </div>

        <!-- Identity Number -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="identity_number" value="{{ __('Identity Number') }}" />
            <x-input id="identity_number" type="text" class="mt-1 block w-full"
                wire:model.live="state.identity_number" autocomplete="identity_number" />
            <x-input-error for="identity_number" class="mt-2" />
        </div>

        <!-- Signature -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="signature" value="{{ __('Signature') }}" />
            <x-input id="signature" type="file" class="mt-1 block w-full" wire:model.live="state.signature"
                autocomplete="signature" />
            <x-input-error for="signature" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
