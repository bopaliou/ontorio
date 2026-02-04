<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-[#d32f2f] hover:to-[#f87171] focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#cb2d2d] focus:ring-offset-2 transition ease-in-out duration-300 shadow-lg shadow-red-900/20 transform hover:-translate-y-0.5']) }}>
    {{ $slot }}
</button>
