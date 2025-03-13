<div class="flex flex-col col-span-full sm:col-span-6 md:col-span-6 lg:col-span-8 xl:col-span-8 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Data Tagihan Bulanan</h2>
        </header>
        <div class="pt-6 px-2 pb-0">
            <div id="bar-chart" class="w-full min-h-[300px] flex items-center justify-center">
                <p id="no-data-message" class="text-gray-500 dark:text-gray-400 hidden">Belum Ada Data</p>
            </div>
        </div>
    </div>
</div>

@php
    // Konversi data dari Laravel ke format JSON untuk JavaScript
    $chartData = $dataDocuments->map(function ($doc) {
        return [
            'month' => date('M', strtotime($doc->created_at . '-01')), // Ambil bulan
            'year' => date('Y', strtotime($doc->created_at . '-01')),  // Ambil tahun
            'total' => $doc->total,
        ];
    });

    // Ubah data ke JSON agar bisa digunakan di JavaScript
    $chartDataJson = json_encode($chartData);
@endphp

<script>
    function getCurrentTheme1() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    }

    // Fungsi untuk mengubah angka menjadi format Rupiah
    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
    }

    function initializeChart1(themeMode1) {
        const chartColors1 = themeMode1 === 'dark' ? ["#8470ff"] : ["#8470ff"];
        const backgroundColor1 = themeMode1 === 'dark' ? "#1F2937" : "#FFFFFF";
        const gridBorderColor1 = themeMode1 === 'dark' ? "#374151" : "#dddddd";
        const textColor1 = themeMode1 === 'dark' ? "#E5E7EB" : "#616161";
        const tooltipTheme1 = themeMode1 === 'dark' ? "dark" : "light";

        // Ambil data dari Laravel yang dikirim ke Blade sebagai JSON
        const chartData = @json($chartData);

                // Jika data kosong, tampilkan pesan "Belum Ada Data" dan keluar dari fungsi
            if (!Array.isArray(chartData) || chartData.length === 0) {
        document.getElementById("no-data-message").classList.remove("hidden");
        return;
    }

        // Ambil tahun unik dari data
        const years = [...new Set(chartData.map(item => item.year))];

        // Filter data berdasarkan tahun terbaru
        const latestYear = Math.max(...years);
        const filteredData = chartData.filter(item => item.year == latestYear);

        const chartConfig1 = {
            series: [{
                name: "Total Tagihan",
                data: filteredData.map(item => item.total),
            }],
            chart: {
                type: "bar",
                height: 300,
                width: "100%",
                toolbar: {
                    show: false,
                },
                background: backgroundColor1,
            },
            title: {
                show: false,
            },
            dataLabels: {
                enabled: true, // Aktifkan label di atas bar
                formatter: function (val) {
                    // return formatRupiah(val); // Format label ke Rupiah
                },
                style: {
                    colors: [textColor1],
                    fontSize: "12px",
                    fontFamily: "inherit",
                }
            },
            colors: chartColors1,
            plotOptions: {
                bar: {
                    columnWidth: "50%",
                    borderRadius: 4,
                },
            },
            xaxis: {
                categories: filteredData.map(item => item.month),
                labels: {
                    style: {
                        colors: textColor1,
                        fontSize: "12px",
                        fontFamily: "inherit",
                        fontWeight: 400,
                    },
                },
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return formatRupiah(val); // Format angka di sumbu Y ke Rupiah
                    },
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
                    left: 10,
                },
            },
            fill: {
                opacity: 0.8,
            },
            tooltip: {
                theme: tooltipTheme1,
                y: {
                    formatter: function (val) {
                        return formatRupiah(val); // Format angka di tooltip ke Rupiah
                    }
                }
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