<form method="POST" action="{{ route('management-non-fee.accumulated.' . ($nonManfeeDocument->accumulatedCosts->isNotEmpty() ? 'update' : 'store'), ['id' => $nonManfeeDocument->id, 'accumulated_id' => $nonManfeeDocument->accumulatedCosts->first()->id ?? null]) }}">
    @csrf
    @if($nonManfeeDocument->accumulatedCosts->isNotEmpty())
        @method('PUT')
    @endif

    <x-management-non-fee.accumulated-costs.index 
        :nonManfeeDocument="$nonManfeeDocument"
        :akunOptions="$akunOptions"
        :isEdit="true" 
    />
</form>