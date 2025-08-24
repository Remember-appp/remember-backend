import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const csrf =
    (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';

router.on('before', (event) => {
    event.detail.visit.headers = {
        ...(event.detail.visit.headers || {}),
        'X-CSRF-TOKEN': csrf,
    };
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: { color: '#4B5563' },
});

initializeTheme();
