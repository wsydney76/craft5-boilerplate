import '../css/app.css';

import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log("HMR")
    });
}

Alpine.plugin(focus)

window.Alpine = Alpine
Alpine.start()
