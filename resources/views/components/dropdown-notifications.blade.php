@props([
    'align' => 'right',
])

<div class="relative inline-flex" x-data="{ open: false }">
    <button
        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 lg:hover:bg-gray-200 dark:hover:bg-gray-700/50 dark:lg:hover:bg-gray-800 rounded-full"
        :class="{ 'bg-gray-200 dark:bg-gray-800': open }" aria-haspopup="true"
        @click.prevent="open = !open; fetchNotifications()" :aria-expanded="open">
        <span class="sr-only">Notifications</span>
        <svg class="fill-current text-gray-500/80 dark:text-gray-400/80" width="16" height="16" viewBox="0 0 16 16"
            xmlns="http://www.w3.org/2000/svg">
            <path
                d="M7 0a7 7 0 0 0-7 7c0 1.202.308 2.33.84 3.316l-.789 2.368a1 1 0 0 0 1.265 1.265l2.595-.865a1 1 0 0 0-.632-1.898l-.698.233.3-.9a1 1 0 0 0-.104-.85A4.97 4.97 0 0 1 2 7a5 5 0 0 1 5-5 4.99 4.99 0 0 1 4.093 2.135 1 1 0 1 0 1.638-1.148A6.99 6.99 0 0 0 7 0Z" />
            <path
                d="M11 6a5 5 0 0 0 0 10c.807 0 1.567-.194 2.24-.533l1.444.482a1 1 0 0 0 1.265-1.265l-.482-1.444A4.962 4.962 0 0 0 16 11a5 5 0 0 0-5-5Zm-3 5a3 3 0 0 1 6 0c0 .588-.171 1.134-.466 1.6a1 1 0 0 0-.115.82 1 1 0 0 0-.82.114A2.973 2.973 0 0 1 11 14a3 3 0 0 1-3-3Z" />
        </svg>
        <div id="notificationBadge"
            class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 border-2 border-gray-100 dark:border-gray-900 rounded-full hidden">
        </div>
    </button>
    <div class="origin-top-right z-10 absolute top-full -mr-48 sm:mr-0 min-w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1 {{ $align === 'right' ? 'right-0' : 'left-0' }}"
        @click.outside="open = false" @keydown.escape.window="open = false" x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" x-cloak>
        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase pt-1.5 pb-2 px-4">Notifications
        </div>
        <ul id="notificationContent" class="max-h-60 overflow-y-auto">
            <li class="p-4 text-center text-gray-500 dark:text-gray-400">Memuat notifikasi...</li>
        </ul>
        <div id="viewAllNotifications" class="hidden p-2 text-center">
            <a href="{{ config('app.url') }}/notifications"
                class="text-blue-500 dark:text-blue-400 text-sm font-semibold hover:underline">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<script>
    window.APP_URL = "{{ config('app.url') }}";
</script>

<script>
    function fetchNotifications() {
        let content = document.getElementById("notificationContent");
        let badge = document.getElementById("notificationBadge");
        let viewAll = document.getElementById("viewAllNotifications");

        if (!content || !badge || !viewAll) {
            console.error("Kontainer notifikasi atau badge tidak ditemukan!");
            return;
        }

        content.innerHTML = '<li class="p-4 text-center text-gray-500 dark:text-gray-400">Memuat notifikasi...</li>';

        fetch(window.APP_URL + "/notifications/json", {
                headers: {
                    "Accept": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                content.innerHTML = '';
                viewAll.classList.add("hidden");

                if (!data.notifications || data.notifications.length === 0) {
                    content.innerHTML = `
                <li class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada notifikasi baru.
                </li>`;
                    badge.style.display = "none";
                    return;
                }

                let unreadCount = 0;
                let notificationsToShow = data.notifications.slice(0, 3); // ✅ Ambil hanya 3 notifikasi terbaru

                notificationsToShow.forEach(notification => {
                    let icon = getNotificationIcon(notification.type);
                    let cleanedMessage = notification.message.replace(/Lihat detail:\s*https?:\/\/[^\s]+/g,
                        '').trim();
                    let displayMessage = cleanedMessage.length > 60 ? cleanedMessage.substring(0, 60) +
                        "..." : cleanedMessage;

                    // ✅ Cek apakah notifikasi belum terbaca
                    let isUnread = !notification.read_at;
                    if (isUnread) {
                        unreadCount++;
                    }

                    content.innerHTML += `
                <li class="border-b border-gray-200 dark:border-gray-700/60 last:border-0 ${isUnread ? 'bg-gray-200 dark:bg-gray-700' : 'bg-white dark:bg-gray-800'}">
                    <a href="${notification.url}" class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/20" onclick="markAsRead(${notification.id})">
                        <span class="block text-sm mb-2">
                            ${icon} <span class="font-medium text-gray-800 dark:text-gray-100">Notification</span>
                            ${displayMessage}
                        </span>
                        <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">
                            ${notification.timestamp}
                        </span>
                    </a>
                </li>
            `;
                });

                // Jika lebih dari 3 notifikasi, tampilkan link "View All"
                if (data.notifications.length > 3) {
                    viewAll.classList.remove("hidden");
                }

                // ✅ Perbarui badge jumlah notifikasi belum terbaca
                updateNotificationBadge(unreadCount);
            })
            .catch(error => {
                console.error('Gagal mengambil notifikasi:', error);
                content.innerHTML = `
            <li class="p-4 text-center text-red-500">
                Gagal memuat notifikasi.
            </li>`;
            });
    }

    // ✅ Fungsi untuk menandai notifikasi sebagai telah dibaca
    function markAsRead(notificationId) {
        fetch(`${window.APP_URL}/notifications/${notificationId}/mark-as-read`, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotifications(); // Refresh daftar notifikasi agar warna berubah
                }
            })
            .catch(error => console.error("Gagal menandai notifikasi sebagai dibaca:", error));
    }

    // ✅ Fetch jumlah notifikasi belum dibaca saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function() {
        fetchNotifications(); // ✅ Panggil saat halaman pertama kali dimuat
    });

    // ✅ Fungsi untuk memperbarui badge
    function updateNotificationBadge(count) {
        let badge = document.getElementById("notificationBadge");
        badge.style.display = count > 0 ? "inline-flex" : "none";
    }

    // ✅ Fungsi untuk menampilkan ikon notifikasi berdasarkan tipe
    function getNotificationIcon(type) {
        let icons = {
            'info': 'ℹ️',
            'warning': '⚠️',
            'success': '✅',
            'error': '❌',
            'message': '📩',
            'default': '📢'
        };
        return icons[type] || icons['default'];
    }
</script>
