<form method="POST" action="{{ route('non-management-fee.accumulated.update', [
    'id' => $nonManfeeDocument->id, 
    'accumulated_id' => optional($nonManfeeDocument->accumulatedCosts->first())->id
]) }}" id="accumulatedForm">
    @csrf
    @method('PUT') 

    <x-non-management-fee.accumulated-costs.index 
        :nonManfeeDocument="$nonManfeeDocument"
        :akunOptions="$akunOptions"
        :isEdit="true" 
    />
</form>