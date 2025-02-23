<div
    class="flex flex-col col-span-full sm:col-span-6 md:col-span-6 lg:col-span-8 xl:col-span-8 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Tagihan Bulanan dan Tahunan</h2>
            <!-- Menu button -->
        </header>
        <div class="pt-6 px-2 pb-0">
            <div id="bar-chart"></div>
        </div>
    </div>
</div>

<script>
    function getCurrentTheme1() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    }

    function initializeChart1(themeMode1) {
        const chartColors1 = themeMode1 === 'dark' ? ["#8470ff"] : ["#8470ff"];
        const backgroundColor1 = themeMode1 === 'dark' ? "#1F2937" : "#FFFFFF";
        const gridBorderColor1 = themeMode1 === 'dark' ? "#374151" : "#dddddd";
        const textColor1 = themeMode1 === 'dark' ? "#E5E7EB" : "#616161";
        const tooltipTheme1 = themeMode1 === 'dark' ? "dark" : "light";

        const chartConfig1 = {
            series: [{
                name: "Sales",
                data: [50, 40, 300, 320, 500, 350, 200, 230, 500],
            }],
            chart: {
                type: "bar",
                height: 240,
                toolbar: {
                    show: false,
                },
                background: backgroundColor1,
            },
            title: {
                show: "",
            },
            dataLabels: {
                enabled: false,
            },
            colors: chartColors1,
            plotOptions: {
                bar: {
                    columnWidth: "40%",
                    borderRadius: 2,
                },
            },
            xaxis: {
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                labels: {
                    style: {
                        colors: textColor1,
                        fontSize: "12px",
                        fontFamily: "inherit",
                        fontWeight: 400,
                    },
                },
                categories: [
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
            },
            yaxis: {
                labels: {
                    style: {
                        colors: textColor1,
                        fontSize: "12px",
                        fontFamily: "inherit",
                        fontWeight: 400,
                    },
                },
            },
            grid: {
                show: true,
                borderColor: gridBorderColor1,
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                padding: {
                    top: 5,
                    right: 20,
                },
            },
            fill: {
                opacity: 0.8,
            },
            tooltip: {
                theme: tooltipTheme1,
            },
        };

        const chart1 = new ApexCharts(document.querySelector("#bar-chart"), chartConfig1);
        chart1.render();
    }

    const currentTheme1 = getCurrentTheme1();
    initializeChart1(currentTheme1);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === "class") {
                const newTheme = getCurrentTheme1();
                document.querySelector("#bar-chart").innerHTML = "";
                initializeChart1(newTheme);
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
    });
</script>
