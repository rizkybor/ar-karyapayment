<div
    class="flex flex-col col-span-full sm:col-span-6 md:col-span-6 lg:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex flex-wrap justify-between items-center gap-2 mb-10">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Status Payment
            </h2>
            <h6 class="text-sm text-gray-600 dark:text-gray-400">
                Total Invoices Terbuat: {{ number_format($totalInvoices, 0, ',', '.') }}
            </h6>
        </header>
        <div class="mt-2 flex justify-center items-center md:h-64 h-74">
            <div id="pie-chart"></div>
        </div>
    </div>
</div>

@php
    // Hitung jumlah invoice berdasarkan kategori status
    $draftCount = $dataInvoices->where('status', 0)->count();
    $onProgressCount = $dataInvoices->whereIn('status', [1, 2, 3, 4, 5, 6, 9])->count();
    $rejectedCount = $dataInvoices->where('status', 99)->count();
    $completedCount = $dataInvoices->where('status', 100)->count();

    // Hitung total semua invoice
    $totalInvoices = $draftCount + $onProgressCount + $rejectedCount + $completedCount;

    // Menghindari error jika total 0
    if ($totalInvoices === 0) {
        $chartData = [0, 0, 0, 0];
    } else {
        $chartData = [
            round(($draftCount / $totalInvoices) * 100),
            round(($onProgressCount / $totalInvoices) * 100),
            round(($rejectedCount / $totalInvoices) * 100),
            round(($completedCount / $totalInvoices) * 100),
        ];

        // Koreksi jika total tidak tepat 100% akibat pembulatan
        $totalRounded = array_sum($chartData);
        $difference = 100 - $totalRounded;

        // Tambahkan atau kurangi dari kategori terakhir agar total tetap 100%
        $chartData[count($chartData) - 1] += $difference;
    }
@endphp

<script>
    function renderChart() {
        const isDarkMode = localStorage.getItem("dark-mode") === "true";
        const chartData = {!! json_encode($chartData) !!}; // Encode data sebagai array numerik
        const chartLabels = ["Draft", "On Progress", "Rejected", "Completed"]; // Tambahkan label Completed

        console.log("Chart Data:", chartData); // Debugging

        const chartConfig2 = {
            series: chartData, // Persentase data
            labels: chartLabels, // Label kategori
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
                    let count = Math.round((val / 100) * {!! json_encode($totalInvoices) !!}); // Hitung jumlah dokumen asli
                    return `${count} Invoices (${Math.round(val)}%)`; // Format tanpa desimal
                },
                style: {
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: "400"
                }
            },
            colors: isDarkMode ? ["#6366F1", "#FBBF24", "#EF4444", "#22C55E"] : ["#6366F1", "#F59E0B", "#DC2626", "#10B981"], 
            // Warna tambahan untuk Completed
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
                        let count = Math.round((val / 100) * {!! json_encode($totalInvoices) !!}); // Hitung jumlah dokumen asli
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