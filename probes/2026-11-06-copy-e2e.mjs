import { chromium } from 'playwright';

const origin = process.env.PROBE_ORIGIN ?? 'http://127.0.0.1:8000';

const browser = await chromium.launch({ channel: 'chrome', headless: true });

try {
    const context = await browser.newContext();
    await context.grantPermissions(['clipboard-read', 'clipboard-write'], { origin });
    const page = await context.newPage();
    await page.goto(origin + '/', { waitUntil: 'load' });

    await page.click('[data-element="EL-005"]');
    await page.waitForFunction(
        () => document.querySelector('[data-element="EL-010"]') !== null,
        { timeout: 10000 },
    );

    const clipboard = await page.evaluate(() => navigator.clipboard.readText());
    const message = await page.textContent('[data-element="EL-010"]');

    if (clipboard !== 'first line\nsecond line') {
        throw new Error(`Unexpected clipboard text: ${JSON.stringify(clipboard)}`);
    }

    console.log(JSON.stringify({ passed: true, clipboard, message }, null, 2));
    await context.close();
} finally {
    await browser.close();
}
