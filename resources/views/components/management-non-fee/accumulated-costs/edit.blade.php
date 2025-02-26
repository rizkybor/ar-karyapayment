<form method="POST" action="{{ route('management-non-fee.update', $nonManfeeDocument->id) }}">
    @csrf
    @method('PUT')

    <x-management-non-fee.accumulated-costs.index 
        :nonManfeeDocument="$nonManfeeDocument" 
        :isEdit="true" />

    {{-- Submit Button --}}
    <div class="flex justify-end mt-6">
        <x-secondary-button onclick="window.location='{{ route('management-non-fee.index') }}'">
            Cancel
        </x-secondary-button>
        <x-button-action color="blue" type="submit">
            Update
        </x-button-action>
    </div>
</form>