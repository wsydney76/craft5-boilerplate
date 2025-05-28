import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import viteCompression from 'vite-plugin-compression';

// https://vitejs.dev/config/
/**
 * @type {import('vite').UserConfig}
 */
export default ({command}) => {
    return {
        plugins: [
            tailwindcss(),
            viteCompression({
                filter: /\.(js|mjs|json|css|map)$/i,
            })
        ],
        base: command === 'serve' ? '' : '/assets/dist/',
        build: {
            // Create manifest.json file
            // will be created in the outDir directory/.vite
            // the 'manifestPath' setting in the config/vite.php file must be set to the same path
            manifest: true,

            // Don't rely on 'assets' as the default value for 'assetsDir' like this:
            // outDir: path.resolve(__dirname, 'web/dist/'),
            // because it will delete other directories in the 'outDir' directory on vite build, like image transforms.

            outDir: path.resolve(__dirname, 'web/dist/assets/'),
            assetsDir: './',

            // The root js file
            rollupOptions: {
                input: {
                    app: path.resolve(__dirname, 'resources/js/app.js'),
                },
            }
        },

        // Enable use of @css alias in JS and CSS files
        resolve: {
            alias: {
                '@css': path.resolve(__dirname, 'resources/css'),
            },
        },

        // Vite dev server options, without any restrictions
        server: {
            host: true,
            port: 5173,
            strictPort: true,
            allowedHosts: true,
            cors: true,
        },
    };
};
