@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border border-line bg-surface px-3.5 py-2.5 text-base text-ink placeholder:text-muted focus:border-ink focus:ring-2 focus:ring-ink/10']) }}>
