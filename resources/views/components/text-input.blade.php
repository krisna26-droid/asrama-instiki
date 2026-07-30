@props(['disabled' => false])

<input
    @disabled($disabled)

    {{ $attributes->merge([
        'class' => '
            w-full
            rounded-lg
            border
            border-slate-300
            bg-white
            px-4
            py-2.5
            text-slate-900
            placeholder:text-slate-400
            shadow-sm
            transition
            focus:border-[#ed1c24]
            focus:ring-2
            focus:ring-[#ed1c24]/20
            focus:outline-none
            disabled:bg-slate-100
            disabled:text-slate-500
        '
    ]) }}
>