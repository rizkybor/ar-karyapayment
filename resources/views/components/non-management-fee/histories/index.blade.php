<!-- Modal History -->
<div id="historyModal"
    class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden transition-opacity duration-300 ease-out px-4">

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
        <div id="historyContent"
            class="space-y-2 max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
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
    modal.classList.add("flex");
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

            // ✅ Ubah object menjadi array agar bisa di-loop dengan `forEach`
            let historyArray = Object.values(data);

            if (!Array.isArray(historyArray)) {
                console.error("Data yang diterima bukan array:", data);
                historyContent.innerHTML =
                    `<p class="text-red-500 text-center">Terjadi kesalahan saat mengambil data.</p>`;
                return;
            }

            if (historyArray.length === 0) {
                historyContent.innerHTML = `
                    <div class="text-center p-4">
                        <span class="block text-4xl">📄</span>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">
                            Belum ada Riwayat Dokumen.
                        </p>
                    </div>`;
                return;
            }

            // ✅ Mapping status untuk menjelaskan arti dari angka status
            const statusMap = {
                '0': 'Draft',
                '1': 'Checked by Kadiv',
                '2': 'Checked by Perbendaharaan',
                '3': 'Checked by Mgr. Anggaran',
                '4': 'Checked by Dir. Keuangan',
                '5': 'Checked by Pajak',
                '6': 'Done at Perbendaharaan',
                '100': 'Finished', // status belum digunakan
                '101': 'Canceled', // status belum digunakan
                '102': 'Revised',
                '103': 'Rejected'
            };

            // ✅ Menampilkan hasil history dengan format lebih baik
            historyArray.forEach(item => {
                let statusText = statusMap[item.status] || 'Unknown Status'; // Ambil deskripsi status atau 'Unknown'

                historyContent.innerHTML += `
                <div class="p-4 border-b border-gray-300 dark:border-gray-700 flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                        📜
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Status Dokumen: ${statusText}
                        </p>
                        ${item.notes ? `<p class="text-sm text-gray-600 dark:text-gray-300">${item.notes}</p>` : ''}
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Oleh: <span class="font-medium">${item.user}</span> • ${item.timestamp} wib
                        </p>
                    </div>
                </div>
            `;
            });

            // ✅ Jika data lebih dari 5, tambahkan class scrollable
            if (historyArray.length > 5) {
                historyContent.classList.add("max-h-60", "overflow-y-auto", "scrollbar-thin", "scrollbar-thumb-gray-300", "dark:scrollbar-thumb-gray-700", "scrollbar-track-transparent");
            } else {
                historyContent.classList.remove("max-h-60", "overflow-y-auto", "scrollbar-thin", "scrollbar-thumb-gray-300", "dark:scrollbar-thumb-gray-700", "scrollbar-track-transparent");
            }
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
            modal.classList.remove("flex");
            modal.classList.add("hidden");
        }, 200);
    }
}
</script>
