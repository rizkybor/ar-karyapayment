<form method="POST"
    action="{{ route('non-management-fee.accumulated.update', [
        'id' => $nonManfeeDocument->id,
        'accumulated_id' => optional($nonManfeeDocument->accumulatedCosts->first())->id,
    ]) }}"
    id="accumulatedForm">
    @csrf
    @method('PUT')

    @if ($errors->any())
        <div id="error-alert" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-md transition-opacity duration-500">
            <strong class="font-semibold">Oops! Ada kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-non-management-fee.accumulated-costs.index :nonManfeeDocument="$nonManfeeDocument" :akunOptions="$akunOptions" :isEdit="true" />
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let errorAlert = document.getElementById("error-alert");

        if (errorAlert) {
            setTimeout(() => {
                errorAlert.classList.add("opacity-0"); // Ubah opacity agar menghilang
                setTimeout(() => {
                    errorAlert.style.display = "none"; // Hilangkan dari DOM
                }, 500);
            }, 2000); 
        }
    });
</script>