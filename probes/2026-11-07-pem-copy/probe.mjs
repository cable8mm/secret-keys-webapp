import { chromium } from 'playwright';

const origin = process.env.PROBE_ORIGIN ?? 'http://127.0.0.1:8000';

const browser = await chromium.launch({ channel: 'chrome', headless: true });

try {
    const context = await browser.newContext();
    await context.grantPermissions(['clipboard-read', 'clipboard-write'], { origin });
    const page = await context.newPage();
    const badRequests = [];
    page.on('response', (response) => {
        if (response.status() >= 400) {
            badRequests.push(`${response.status()} ${response.url()}`);
        }
    });
    await page.goto(origin + '/', { waitUntil: 'load' });

    const row = page.locator('tr', { hasText: 'E2E PEM Key' });
    await row.locator('[data-element="EL-005"]').click();
    await page.waitForTimeout(5000);

    const clipboard = await page.evaluate(() => navigator.clipboard.readText());
    const message = await page.textContent('[data-element="EL-010"]').catch(() => null);

    const expected = '-----BEGIN KEY-----\nabc\ndef\n';
    const observation = {
        clipboard,
        clipboardMatches: clipboard === expected,
        confirmationMessage: message?.trim() ?? null,
        badRequests: badRequests.slice(0, 5),
    };

    console.log(JSON.stringify(observation, null, 2));

    if (! observation.clipboardMatches) {
        throw new Error(`Unexpected clipboard text: ${JSON.stringify(clipboard)}`);
    }

    await context.close();
} finally {
    await browser.close();
}
