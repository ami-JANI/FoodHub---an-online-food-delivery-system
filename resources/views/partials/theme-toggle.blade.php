<button
    type="button"
    onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');"
    class="{{ $class ?? 'text-sm bg-white/10 hover:bg-white/20 transition px-3 py-1.5 rounded-full' }}"
    title="Toggle dark / light theme"
>
    <span class="dark:hidden">🌙</span>
    <span class="hidden dark:inline">☀️</span>
</button>
