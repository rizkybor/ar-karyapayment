<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Daftar Pengguna</h1>
        </div>

        <!-- Cards Container -->
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header
                    class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="font-semibold dark:text-gray-100 py-3">List Users</h2>

                    <form method="GET" action="{{ route('list_users') }}" class="flex gap-2">
                        <input type="text" name="search" placeholder="Cari nama/email..."
                            class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 font-medium px-3 py-2 h-9 rounded-lg shadow-sm focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all ease-in-out duration-200"
                            value="{{ request('search') }}" />
                        <button type="submit"
                            class="bg-blue-600  text-gray-700 dark:text-gray-200 px-4 py-2 h-9 rounded-lg shadow hover:bg-blue-700 transition-all duration-200">
                            Cari
                        </button>
                    </form>
                </header>

                <!-- Table Section -->
                <div class="p-3 overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="p-2 whitespace-nowrap text-left">#</th>
                                <th class="p-2 whitespace-nowrap text-left">Nama</th>
                                <th class="p-2 whitespace-nowrap text-left">Email</th>
                                <th class="p-2 whitespace-nowrap text-left">Role</th>
                                                                <th class="p-2 whitespace-nowrap text-left">Department</th>
                                <th class="p-2 whitespace-nowrap text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="p-2 text-sm text-gray-700 dark:text-gray-200">
                                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                    </td>
                                    <td class="p-2 text-sm text-gray-700 dark:text-gray-200">{{ $user->name }}</td>
                                    <td class="p-2 text-sm text-gray-700 dark:text-gray-200">{{ $user->email }}</td>
                                    <td class="p-2 text-sm text-gray-700 dark:text-gray-200">{{ $user->role }}</td>
                                     <td class="p-2 text-sm text-gray-700 dark:text-gray-200">{{ $user->department }}</td>
                                    <td class="p-2 text-sm text-gray-700 dark:text-gray-200">
                                        <a href="{{ route('list_users.edit', $user->id) }}"
                                            class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">Edit</a>

                                        <form action="{{ route('list_users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                            class="inline-block ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-semibold text-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
