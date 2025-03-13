<div
    class="flex flex-col col-span-full sm:col-span-6 md:col-span-6 lg:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex flex-wrap justify-between items-center gap-2 mb-10">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Seluruh Invoices
            </h2>
            <h6 class="text-sm text-gray-600 dark:text-gray-400">
                Terbuat: {{ number_format($totalInvoices, 0, ',', '.') }}
            </h6>
        </header>
        <div class="mt-2 flex justify-center items-center md:h-64 h-74">
            <div id="pie-chart">
                <p id="no-data-message" class="text-gray-500 dark:text-gray-400 hidden">Belum ada data tagihan invoice.
                </p>
            </div>
        </div>
    </div>
</div>

@php
    // Hitung jumlah invoice berdasarkan kategori status
    $activeCount = $dataInvoices->where('is_active', true)->count();
    $notActiveCount = $docExpired;
    $rejectedCount = $dataInvoices->where('status', 103)->count();
    $completedCount = $dataInvoices->where('status', 100)->count();

    // Hitung total semua invoice
    $totalInvoices = $activeCount + $notActiveCount + $rejectedCount + $completedCount;

    // Jika semua data kosong, berikan default nilai 0
    if ($totalInvoices === 0) {
        $chartData = [0, 0, 0, 0];
        $chartLabels = ['Active', 'Not Active', 'Rejected', 'Completed'];
    } else {
        $chartData = [
            round(($activeCount / $totalInvoices) * 100),
            round(($notActiveCount / $totalInvoices) * 100),
            round(($rejectedCount / $totalInvoices) * 100),
            round(($completedCount / $totalInvoices) * 100),
        ];

        // Koreksi jika total tidak tepat 100% akibat pembulatan
        $totalRounded = array_sum($chartData);
        $difference = 100 - $totalRounded;

        // Tambahkan atau kurangi dari kategori terakhir agar total tetap 100%
        $chartData[count($chartData) - 1] += $difference;

        $chartLabels = ['Active', 'Not Active', 'Rejected', 'Completed'];
    }
@endphp

<script>
    function renderChart() {
        const isDarkMode = localStorage.getItem("dark-mode") === "true";
        const chartData = {!! json_encode($chartData) !!};
        const chartLabels = {!! json_encode($chartLabels) !!};
        const totalInvoices = {!! json_encode($totalInvoices) !!};

        console.log("Chart Data:", chartData); // Debugging

        // Cek jika semua data 0, maka tampilkan pesan "Belum ada data"
        if (totalInvoices === 0) {
            document.getElementById("no-data-message").classList.remove("hidden");
            return;
        }

        const chartConfig2 = {
            series: chartData,
            labels: chartLabels,
            chart: {
                type: "pie",
                width: 280,
                height: 280,
                toolbar: {
                    show: false
                },
                background: "transparent",
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    let count = Math.round((val / 100) * totalInvoices);
                    return `${count} Invoices (${Math.round(val)}%)`;
                },
                style: {
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: "400"
                }
            },
            colors: isDarkMode ? ["#6366F1", "#FBBF24", "#EF4444", "#22C55E"] : ["#6366F1", "#F59E0B", "#DC2626",
                "#10B981"
            ],
            legend: {
                show: true,
                position: "bottom",
                labels: {
                    colors: isDarkMode ? "#E5E7EB" : "#1F2937"
                }
            },
            theme: {
                mode: isDarkMode ? 'dark' : 'light'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        let count = Math.round((val / 100) * totalInvoices);
                        return new Intl.NumberFormat("id-ID", {
                            style: "decimal"
                        }).format(count) + " Invoices (" + Math.round(val) + "%)";
                    }
                }
            }
        };

        const chartElement = document.querySelector("#pie-chart");
        if (!chartElement) {
            console.error("Pie Chart element not found!");
            return;
        }

        chartElement.innerHTML = ""; // Reset sebelum render ulang
        const chart2 = new ApexCharts(chartElement, chartConfig2);
        chart2.render();
    }

    document.addEventListener("DOMContentLoaded", renderChart);
    document.addEventListener("darkMode", renderChart);
</script>
