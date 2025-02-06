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

<script>
    function renderChart() {
        const isDarkMode = localStorage.getItem("dark-mode") === "true";

        const chartConfig2 = {
            series: [60, 55],
            chart: {
                type: "pie",
                width: 280,
                height: 280,
                toolbar: {
                    show: false,
                },
                background: "transparent",
            },
            dataLabels: {
                enabled: false,
            },
            colors: isDarkMode ? ["#8470ff", "#FFFFFF"] : ["#8470ff", "#1f2937"],
            legend: {
                show: false,
            },
            theme: {
                mode: isDarkMode ? 'dark' : 'light',
            },
        };

        const chartElement = document.querySelector("#pie-chart");
        if (chartElement) {
            chartElement.innerHTML = "";
            const chart2 = new ApexCharts(chartElement, chartConfig2);
            chart2.render();
        }
    }

    document.addEventListener("DOMContentLoaded", renderChart);
    document.addEventListener("darkMode", renderChart);
</script>
