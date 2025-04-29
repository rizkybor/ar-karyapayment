@if ($data->lastPage() > 1)
    <div class="flex justify-center">
        <nav class="flex" role="navigation" aria-label="Navigation">
            {{-- Previous Button --}}
            <div class="mr-2">
                @if ($data->currentPage() > 1)
                    <a href="{{ $data->previousPageUrl() }}"
                        class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 shadow-sm">
                        <span class="sr-only">Previous</span>
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                        </svg>
                    </a>
                @else
                    <span
                        class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-300 dark:text-gray-600 shadow-sm">
                        <span class="sr-only">Previous</span>
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                        </svg>
                    </span>
                @endif
            </div>

            {{-- Page Numbers --}}
            <ul class="inline-flex text-sm font-medium -space-x-px rounded-lg shadow-sm">
                @php
                    $start = max(1, $data->currentPage() - 2);
                    $end = min($data->lastPage(), $data->currentPage() + 2);

                    if ($start > 1) {
                        echo '<li>
                        <a href="' .
                            $data->url(1) .
                            '" class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300">1</a>
                    </li>';
                        if ($start > 2) {
                            echo '<li>
                            <span class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-400 dark:text-gray-500">...</span>
                        </li>';
                        }
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        if ($i == $data->currentPage()) {
                            echo '<li>
                            <span class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-violet-500">' .
                                $i .
                                '</span>
                        </li>';
                        } else {
                            echo '<li>
                            <a href="' .
                                $data->url($i) .
                                '" class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300">' .
                                $i .
                                '</a>
                        </li>';
                        }
                    }

                    if ($end < $data->lastPage()) {
                        if ($end < $data->lastPage() - 1) {
                            echo '<li>
                            <span class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-400 dark:text-gray-500">...</span>
                        </li>';
                        }
                        echo '<li>
                        <a href="' .
                            $data->url($data->lastPage()) .
                            '" class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300">' .
                            $data->lastPage() .
                            '</a>
                    </li>';
                    }
                @endphp
            </ul>

            {{-- Next Button --}}
            <div class="ml-2">
                @if ($data->hasMorePages())
                    <a href="{{ $data->nextPageUrl() }}"
                        class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300 shadow-sm">
                        <span class="sr-only">Next</span>
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                        </svg>
                    </a>
                @else
                    <span
                        class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-300 dark:text-gray-600 shadow-sm">
                        <span class="sr-only">Next</span>
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                        </svg>
                    </span>
                @endif
            </div>
        </nav>
    </div>
@endif
