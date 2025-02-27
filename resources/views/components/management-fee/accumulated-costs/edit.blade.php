{{-- <form method="POST" action="{{ route('management-fee.update', $ManfeeDocument->id) }}"> --}}
<form method="POST" action="">
    @csrf
    @method('PUT')

    <x-management-fee.accumulated-costs.index :isEdit="true" />
    {{-- <x-management-fee.accumulated-costs.index :ManfeeDocument="$ManfeeDocument" :isEdit="true" /> --}}

    {{-- Submit Button --}}
    <div class="flex justify-end mt-6">
        {{-- <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'"> --}}
        {{-- <x-secondary-button onclick="window.location=''">
            Cancel
        </x-secondary-button>
        <x-button-action color="blue" type="submit">
            Update
        </x-button-action> --}}
    </div>
</form>
