<script>
    systemPreference = localStorage.getItem('systemPreference') === "true";
    prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

    if (systemPreference) {
        prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.classList.toggle('dark', prefersDarkScheme.matches);
    } else {
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.querySelector('html').classList.add('dark');
        } else {
            document.querySelector('html').classList.remove('dark');
        }
    }

    // Écoutez les changements de préférence système
    prefersDarkScheme.addEventListener('change', (e) => {
        if (systemPreference) {
            document.documentElement.classList.toggle('dark', e.matches);
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
</script>