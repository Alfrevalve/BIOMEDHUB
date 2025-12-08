<div class="space-y-3">
    <div class="text-sm text-slate-600">
        <p><span class="font-semibold">De:</span> {{ $message->sender?->name ?? 'Usuario' }}</p>
        <p><span class="font-semibold">Fecha:</span> {{ optional($message->created_at)->timezone('America/Lima')->format('d/m/Y H:i') }}</p>
    </div>
    <div class="prose max-w-none text-slate-800">
        {!! $message->body !!}
    </div>
</div>
