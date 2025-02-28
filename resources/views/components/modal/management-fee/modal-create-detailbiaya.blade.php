@props(['manfeeDoc'])

<div x-data="{ modalOpen: false }">
    <div class="flex justify-between items-center mb-3">
        <x-button-action class="px-4 py-2 bg-violet-500 text-white rounded-md" @click="modalOpen = true">
            Tambah Detail Biaya
        </x-button-action>
    </div>

    <!-- Modal -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-3xl w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tambah Detail Biaya test</h3>

            <!-- Jenis Biaya -->
            <div class="mb-4">
                <label for="jenis_biaya" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                    Biaya</label>
                <select id="jenis_biaya" name="jenis_biaya"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring focus:ring-blue-500">
                    <option>Biaya Personil</option>
                </select>
            </div>
            <!-- Select Manfee -->


            <div class="flex justify-end items-center mb-3">
                {{-- <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Manfee</label>
                    <div class="flex items-center gap-4 mt-1">
                        <label class="inline-flex items-center">
                            <input type="radio" name="manfee" value="non" class="form-radio text-blue-500"
                                disabled>
                            <span class="ml-2">Non Manfee</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="manfee" value="manfee" class="form-radio text-blue-500"
                                checked>
                            <span class="ml-2">Manfee</span>
                        </label>
                    </div>
                </div> --}}
                <x-modal.management-fee.modal-create-data-detailbiaya />
            </div>

            <!-- List Detail Biaya Personil -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="p-2">No</th>
                            <th class="p-2">Account</th>
                            <th class="p-2">Uraian</th>
                            <th class="p-2">Nilai Biaya</th>
                            <th class="p-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2">1</td>
                            <td class="p-2">10011</td>
                            <td class="p-2">Gaji</td>
                            <td class="p-2">Rp. 50.000</td>
                            <td class="p-2 flex gap-2 justify-center">
                                <button class="px-2 py-1 bg-yellow-500 text-white rounded-md">Edit</button>
                                <button class="px-2 py-1 bg-red-500 text-white rounded-md">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">2</td>
                            <td class="p-2">10032</td>
                            <td class="p-2">BPJS TK</td>
                            <td class="p-2">Rp. 50.000</td>
                            <td class="p-2 flex gap-2 justify-center">
                                <button class="px-2 py-1 bg-yellow-500 text-white rounded-md">Edit</button>
                                <button class="px-2 py-1 bg-red-500 text-white rounded-md">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="p-2 text-right font-semibold">Total</td>
                            <td class="p-2 font-semibold">Rp. 100.000</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
