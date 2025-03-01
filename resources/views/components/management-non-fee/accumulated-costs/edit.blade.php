<form method="POST" action="{{ route('management-non-fee.update', $nonManfeeDocument->id) }}">
    @csrf
    @method('PUT')

    <x-management-non-fee.accumulated-costs.index 
        :nonManfeeDocument="$nonManfeeDocument" 
        :akunOptions="$akunOptions"
        :isEdit="true" 
    />
</form>