import { defineConfig } from '@playwright/test';
import path from 'node:path';
const root = process.cwd();
export default defineConfig({
    testDir: './tests/Browser',
    outputDir: './storage/framework/testing/browser-results',
    workers: 1,
    timeout: 30000,
    use: { baseURL: 'http://127.0.0.1:8765', browserName: 'chromium', channel: 'chrome', headless: true, screenshot: 'only-on-failure' },
    webServer: {
        command: 'php artisan serve --host=127.0.0.1 --port=8765',
        url: 'http://127.0.0.1:8765/login',
        reuseExistingServer: false,
        env: {
            APP_ENV:'testing', APP_DEBUG:'true', DB_CONNECTION:'sqlite',
            DB_DATABASE: path.join(root,'storage/framework/testing/browser.sqlite'),
            DB_URL:'', CACHE_STORE:'array', SESSION_DRIVER:'file', APP_URL:'http://127.0.0.1:8765',
            APP_CONFIG_CACHE:path.join(root,'storage/framework/testing/browser-config.php'),
        },
    },
});
