import { readFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright';

const origin = process.env.PROBE_ORIGIN ?? 'http://127.0.0.1:8000';
const pageUrl = new URL('/clipboard-probe.html', origin).href;
const scriptUrl = new URL('/probe.js', origin).href;

const probeHtml = await readFile(
    fileURLToPath(new URL('./index.html', import.meta.url)),
    'utf8',
);
const probeScript = await readFile(
    fileURLToPath(new URL('./probe.js', import.meta.url)),
    'utf8',
);

const samples = [
    { id: 'leading-spaces', text: '   leading spaces' },
    { id: 'trailing-spaces', text: 'trailing spaces   ' },
    { id: 'line-breaks', text: 'line one\nline two\nline three' },
    { id: 'combined', text: '  first line\nsecond line\nthird line  ' },
];

async function createContext(browser, permissions) {
    const context = await browser.newContext();

    await context.route(pageUrl, (route) =>
        route.fulfill({ status: 200, contentType: 'text/html', body: probeHtml }),
    );
    await context.route(scriptUrl, (route) =>
        route.fulfill({
            status: 200,
            contentType: 'text/javascript',
            body: probeScript,
        }),
    );

    await context.grantPermissions(permissions, { origin });

    return context;
}

async function runSamples(context, label) {
    const page = await context.newPage();
    await page.goto(pageUrl, { waitUntil: 'load' });
    await page.bringToFront();

    const results = [];

    for (const sample of samples) {
        results.push(
            await page.evaluate(
                ({ id, text }) => window.clipboardProbe.writeAndRead(text, id),
                sample,
            ),
        );
    }

    await page.close();

    return { label, results };
}

const browser = await chromium.launch({ channel: 'chrome', headless: true });

try {
    const grantedContext = await createContext(browser, [
        'clipboard-read',
        'clipboard-write',
    ]);
    const granted = await runSamples(grantedContext, 'granted');
    await grantedContext.close();

    const deniedContext = await createContext(browser, []);
    const denied = await runSamples(deniedContext, 'denied');
    await deniedContext.close();

    const writeSucceeded = granted.results.every(
        (result) => result.success && result.readBack === result.written,
    );
    const denialObserved = denied.results.every(
        (result) =>
            !result.success &&
            result.stage === 'write' &&
            typeof result.error === 'string' &&
            result.error.startsWith('NotAllowedError'),
    );

    const report = {
        origin,
        pageUrl,
        passed: writeSucceeded && denialObserved,
        writeSucceeded,
        denialObserved,
        granted,
        denied,
    };

    console.log(JSON.stringify(report, null, 2));
    process.exit(report.passed ? 0 : 1);
} finally {
    await browser.close();
}
