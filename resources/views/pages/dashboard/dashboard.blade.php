<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Dashboard</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

            </div>

        </div>

        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">

            <!-- Line chart (Acme Plus) -->
            <x-dashboard-invoice.card-stick :dataDocuments=$dokumenSementara />

            <!-- Line chart (Acme Advanced) -->
            <x-dashboard-invoice.card-rounded :totalInvoices=$totalInvoices :dataInvoices=$dataInvoices :draftCount=$draftCount
                :onProgressCount=$onProgressCount :rejectedCount=$rejectedCount :completedCount=$completedCount/>

            <!-- Card (Customers) -->
            <x-dashboard-invoice.card-table-manfee :dataInvoices=$dataInvoices />

            <x-dashboard-invoice.card-table-nonmanfee :dataInvoices=$dataInvoicesNonFee />

            {{-- <!-- Line chart (Acme Professional) -->
            <x-dashboard.dashboard-card-03 :dataFeed="$dataFeed" />

            <!-- Bar chart (Direct vs Indirect) -->
            <x-dashboard.dashboard-card-04 />

            <!-- Line chart (Real Time Value) -->
            <x-dashboard.dashboard-card-05 />

            <!-- Doughnut chart (Top Countries) -->
            <x-dashboard.dashboard-card-06 />

            <!-- Table (Top Channels) -->
            <x-dashboard.dashboard-card-07 />

            <!-- Line chart (Sales Over Time) -->
            <x-dashboard.dashboard-card-08 />

            <!-- Stacked bar chart (Sales VS Refunds) -->
            <x-dashboard.dashboard-card-09 /> --}}


            {{-- <!-- Card (Reasons for Refunds) -->
            <x-dashboard.dashboard-card-11 />

            <!-- Card (Recent Activity) -->
            <x-dashboard.dashboard-card-12 />

            <!-- Card (Income/Expenses) -->
            <x-dashboard.dashboard-card-13 /> --}}

        </div>

    </div>
</x-app-layout>
