@if ($errors->any())
    <div {{ $attributes }}>
        <div id="error-alert" class="px-4 py-2 rounded-lg text-sm bg-red-500 text-white duration-500">
            <div class="font-medium">{{ __('Whoops! Something went wrong.') }}</div>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>         
    </div>
@endif

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