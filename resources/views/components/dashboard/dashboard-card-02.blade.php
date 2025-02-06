<div class="flex flex-col col-span-full sm:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Status Payment</h2>
            <!-- Menu button -->
        </header>
        <div class="mt-2 flex justify-center items-center h-64">
            <div id="pie-chart" class="hidden sm:block w-full max-w-md"></div>
        </div>
    </div>
</div>

<script>
    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    const chartConfig2 = {
        series: [60, 55],
        chart: {
            type: "pie",
            width: 280,
            height: 280,
            toolbar: {
                show: false,
            },
        },
        title: {
            show: "",
        },
        dataLabels: {
            enabled: false,
        },
        colors: isDarkMode ? ["#1f2937", "#ffb300"] : ["#034234", "#ff8f00"],
        legend: {
            show: false,
        },
        theme: {
            mode: isDarkMode ? 'dark' : 'light',
        },
    };

    const chart2 = new ApexCharts(document.querySelector("#pie-chart"), chartConfig2);
    chart2.render();
</script>
