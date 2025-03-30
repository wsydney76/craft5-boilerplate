import path from 'path';
import tailwindcss from '@tailwindcss/vite';

// https://vitejs.dev/config/
/**
 * @type {import('vite').UserConfig}
 */
export default ({ command }) => {
    return {
        plugins: [
            tailwindcss(),
        ],
        base: command === 'serve' ? '' : '/assets/dist/',
        build: {
            manifest: true,

            // Don't rely on 'assets' as the default value for 'assetsDir' like this:
            // outDir: path.resolve(__dirname, 'web/dist/'),
            // because it will delete other directories in the 'outDir' directory on vite build, like image transforms.

            outDir: path.resolve(__dirname, 'web/dist/assets/'),
            assetsDir: './',
            rollupOptions: {
                input: {
                    app: path.resolve(__dirname, 'resources/js/app.js'),
                },
            }
        },
        publicDir: path.resolve(__dirname, 'resources/public'),
        server: {
            host: true,
            port: 3000,
            strictPort: true,
            allowedHosts: true,
            cors: true,
        },
    };
};
