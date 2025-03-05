@props(['manfeeDoc', 'account_dummy', 'subtotals', 'subtotalBiayaNonPersonil', 'rate_manfee'])

@php
    $accumulated_id = optional($manfeeDoc->accumulatedCosts->first())->id ?? 'new';
@endphp

<form method="POST"
    action="{{ route('management-fee.accumulated.update', [
        'id' => $manfeeDoc->id,
        'accumulated_id' => $accumulated_id,
    ]) }}"
    id="accumulatedForm">
    @csrf
    @method('PUT')

    <x-validation-errors :attributes="$manfeeDoc" :errors="$errors" />

    <x-management-fee.accumulated-costs.index :manfeeDoc="$manfeeDoc" :subtotals="$subtotals" :subtotalBiayaNonPersonil="$subtotalBiayaNonPersonil" :rate_manfee="$rate_manfee"
        :account_dummy="$account_dummy" :isEdit="true" />
</form>
