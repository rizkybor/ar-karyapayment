<!-- Tombol untuk Membuka Modal -->
<x-button-action color="blue" icon="history" onclick="openHistoryModal({{ $nonManfeeDocument['id'] }})">
    Lihat History
</x-button-action>

<!-- Modal History -->
<div id="historyModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-out px-4">

    <div class="relative bg-white dark:bg-gray-800 p-6 rounded-lg shadow-2xl w-full sm:max-w-lg max-h-[70vh] overflow-hidden
                transform transition-all duration-300 scale-95 opacity-0 mx-4 sm:mx-0"
        id="historyModalContent">

        <!-- Header Modal -->
        <div class="flex justify-between items-center border-b pb-3 mb-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Riwayat Dokumen
            </h3>
            <button class="text-gray-500 dark:text-gray-300 hover:text-red-500 transition-all"
                onclick="closeHistoryModal()">
                &times;
            </button>
        </div>

        <!-- Isi History -->
        <div id="historyContent" class="space-y-2 max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
            <p class="text-gray-500 dark:text-gray-400 text-center">Memuat history...</p>
        </div>

        <!-- Tombol Tutup -->
        <div class="mt-4 text-right">
            <x-secondary-button onclick="closeHistoryModal()">Tutup</x-secondary-button>
        </div>
    </div>
</div>

<script>
    function openHistoryModal(documentId) {
        let modal = document.getElementById("historyModal");
        let modalContent = document.getElementById("historyModalContent");
        let historyContent = document.getElementById("historyContent");

        if (!modal || !modalContent || !historyContent) {
            console.error("Modal atau kontainer history tidak ditemukan!");
            return;
        }

        modal.classList.remove("hidden");
        setTimeout(() => {
            modal.classList.add("opacity-100");
            modalContent.classList.remove("scale-95", "opacity-0");
            modalContent.classList.add("scale-100", "opacity-100");
        }, 50);

        if (!documentId) {
            console.error("ID Dokumen tidak ditemukan!");
            return;
        }

        let url = `{{ url('non-management-fee/histories') }}/${documentId}`;

        fetch(url, {
                headers: {
                    "Accept": "application/json"
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP Error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                historyContent.innerHTML = '';

                if (data.error) {
                    historyContent.innerHTML = `<p class="text-red-500 text-center">${data.error}</p>`;
                    return;
                }

                if (data.length === 0) {
                    historyContent.innerHTML = `
                        <div class="text-center p-4">
                            <span class="block text-4xl">📄</span>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">
                                Belum ada Riwayat Dokumen.
                            </p>
                        </div>`;
                    return;
                }

                // ✅ Menampilkan hasil history dengan format lebih baik
                data.forEach(item => {
                    historyContent.innerHTML += `
                    <div class="p-4 border-b border-gray-300 dark:border-gray-700 flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                            📜
                        </span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                ${item.status}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Oleh: <span class="font-medium">${item.user}</span> • ${item.timestamp}
                            </p>
                        </div>
                    </div>
                `;
                });
            })
            .catch(error => {
                console.error('Error fetching history:', error);
                historyContent.innerHTML =
                    '<p class="text-red-500 text-center">Gagal mengambil data history.</p>';
            });
    }

    function closeHistoryModal() {
        let modal = document.getElementById("historyModal");
        let modalContent = document.getElementById("historyModalContent");

        if (modal && modalContent) {
            modal.classList.remove("opacity-100");
            modalContent.classList.remove("scale-100", "opacity-100");
            modalContent.classList.add("scale-95", "opacity-0");

            setTimeout(() => {
                modal.classList.add("hidden");
            }, 200);
        }
    }
</script>