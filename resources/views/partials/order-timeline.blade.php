<div class="relative">
    @php
        $allStatuses = $order->statuses->sortBy('created_at');
        $statusCount = $allStatuses->count();
    @endphp
    @forelse($allStatuses as $i => $status)
        @php
            $isLast = $i === $statusCount - 1;
            $isCurrent = $i === $statusCount - 1;
        @endphp
        <div class="flex items-start gap-4 pb-6 relative">
            @if(!$isLast)
                <div class="absolute left-[15px] top-8 w-0.5 h-full bg-gray-200"></div>
            @endif
            <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm {{ $isCurrent ? 'bg-rar-600 text-white ring-4 ring-rar-100' : 'bg-gray-100 text-gray-400' }}">
                {{ $status->icon() }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-medium {{ $isCurrent ? 'text-rar-700' : 'text-gray-500' }}">{{ $status->statusLabel() }}</p>
                <p class="text-xs text-gray-400">{{ $status->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    @empty
        <p class="text-gray-400 text-sm">Sin registros de estado.</p>
    @endforelse
</div>
