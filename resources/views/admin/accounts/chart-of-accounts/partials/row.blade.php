@php
    $bgClass =
        $depth === 0 ? 'bg-white-light/20 dark:bg-dark/10' : ($depth === 1 ? 'bg-white-light/10 dark:bg-dark/5' : '');
    $fontClass =
        $depth === 0
            ? 'font-bold text-primary text-xs uppercase'
            : ($depth === 1
                ? 'font-semibold text-sm'
                : 'text-sm');
@endphp

<tr class="{{ $bgClass }} {{ $fontClass }} group transition-all duration-300">
    <td class="font-mono text-[10px] w-24">{{ $account->code }}</td>
    <td class="flex items-center">
        <!-- Indentation -->
        @for ($i = 0; $i < $depth; $i++)
            <div class="h-4 w-5 border-l border-gray-300 dark:border-gray-600 ltr:ml-2 rtl:mr-2"></div>
        @endfor

        @if ($depth > 0)
            <div class="h-4 w-3 border-b border-gray-300 dark:border-gray-600 ltr:mr-2 rtl:ml-2"></div>
        @endif

        <span class="{{ $account->is_default ? 'text-blue-600 dark:text-blue-400' : '' }}">
            {{ $account->name }}
        </span>
    </td>
    <td>
        <span class="badge badge-outline-secondary text-[10px] uppercase font-bold px-2 whitespace-nowrap">
            {{ $account->type }}
        </span>
    </td>
    <td>
        @if ($account->is_active)
            <span class="badge badge-outline-success font-bold text-[10px]">Active</span>
        @else
            <span class="badge badge-outline-danger font-bold text-[10px]">Disabled</span>
        @endif
    </td>
    <td class="text-center">
        <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
            <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                {{-- Edit --}}
                <li>
                    <button type="button"
                        @click="toggle(); $dispatch('open-chart-edit', {
                            id: {{ $account->id }},
                            type: @js($account->type),
                            parent_id: @js($account->parent_id ?? ''),
                            code: @js($account->code),
                            name: @js($account->name)
                        })"
                        class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Edit
                    </button>
                </li>

                {{-- Toggle Status --}}
                <li>
                    <form action="{{ route('admin.chart-of-accounts.status', $account) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                            @if($account->is_active)
                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-warning" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="10" y1="15" x2="10" y2="9"></line>
                                    <line x1="14" y1="15" x2="14" y2="9"></line>
                                </svg>
                                Disable
                            @else
                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 8 8 12 12 16"></polyline>
                                    <line x1="16" y1="12" x2="8" y2="12"></line>
                                </svg>
                                Activate
                            @endif
                        </button>
                    </form>
                </li>

                {{-- Delete or System notice --}}
                @if (!$account->is_default)
                    <li>
                        <form action="{{ route('admin.chart-of-accounts.destroy', $account) }}" method="POST"
                            onsubmit="return confirm('Deleting this head is irreversible. Continue?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <span class="flex w-full items-center px-4 py-2 text-xs text-blue-500 italic">
                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-blue-500" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            System Head (Locked)
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </td>
</tr>

@foreach ($account->children as $child)
    @include('admin.accounts.chart-of-accounts.partials.row', ['account' => $child, 'depth' => $depth + 1])
@endforeach
