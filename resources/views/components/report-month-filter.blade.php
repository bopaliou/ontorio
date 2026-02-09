<form method="GET" {{ $attributes->merge(['class' => 'bg-white border border-slate-200 rounded-2xl p-4 flex flex-wrap items-end gap-3']) }}>
    <div>
        <label for="{{ $id }}" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">{{ $label }}</label>
        <input id="{{ $id }}" name="{{ $name }}" type="month" value="{{ $value }}" class="rounded-lg border-slate-300 text-sm" />
    </div>
    <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider">Filtrer</button>
</form>
