<script>
    systemPreference = localStorage.getItem('systemPreference') === "true";
    prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

    if (systemPreference) {
        console.log("Préférence système");
        prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.classList.toggle('dark', prefersDarkScheme.matches);
        console.log(localStorage.getItem('theme'));
    } else {
        console.log("Pas de préférence système");
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.querySelector('html').classList.add('dark');
        } else {
            document.querySelector('html').classList.remove('dark');
        }
        console.log(localStorage.getItem('theme'));
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