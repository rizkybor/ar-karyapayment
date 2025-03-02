<div
    class="flex flex-col col-span-full sm:col-span-6 md:col-span-6 lg:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Status Payment</h2>
        </header>
        <div class="mt-2 flex justify-center items-center h-64">
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

    // Pastikan nilai dalam array benar-benar integer (bukan string)
    $chartData = [(int) $draftCount, (int) $onProgressCount, (int) $rejectedCount, (int) $completedCount];
@endphp

<script>
    function renderChart() {
        const isDarkMode = localStorage.getItem("dark-mode") === "true";
        const chartData = {!! json_encode($chartData) !!}; // Encode data sebagai array numerik
        const chartLabels = ["Draft", "On Progress", "Rejected", "Completed"]; // Tambahkan label Completed

        console.log("Chart Data:", chartData); // Debugging

        const chartConfig2 = {
            series: chartData, // Masukkan array angka
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
                    return new Intl.NumberFormat("id-ID").format(opts.w.globals.series[opts.seriesIndex]) +
                        " (" + val.toFixed(1) + "%)";
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
                        return new Intl.NumberFormat("id-ID", {
                            style: "decimal"
                        }).format(val) + " Invoices";
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