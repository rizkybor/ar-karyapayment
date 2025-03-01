<form method="POST" action="{{ route('non-management-fee.accumulated.' . ($nonManfeeDocument->accumulatedCosts->isNotEmpty() ? 'update' : 'store'), ['id' => $nonManfeeDocument->id, 'accumulated_id' => $nonManfeeDocument->accumulatedCosts->first()->id ?? null]) }}">
    @csrf
    @if($nonManfeeDocument->accumulatedCosts->isNotEmpty())
        @method('PUT')
    @endif

    <x-non-management-fee.accumulated-costs.index 
        :nonManfeeDocument="$nonManfeeDocument"
        :akunOptions="$akunOptions"
        :isEdit="true" 
    />
</form>