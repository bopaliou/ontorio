@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-[#cb2d2d] focus:ring-[#cb2d2d] rounded-xl shadow-sm transition-all duration-200']) }}>
