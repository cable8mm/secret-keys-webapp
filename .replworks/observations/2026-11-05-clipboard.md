# 2026-11-05 Clipboard observation

## Environment

- Host: macOS (darwin), desktop session.
- Product origin: `http://127.0.0.1:8000`, served by `php artisan serve --host=127.0.0.1 --port=8000`.
- Probe: `probes/2026-11-05-clipboard/`.
- Browser for the recorded pass/fail run: Google Chrome launched through Playwright `1.64.1` (`channel: "chrome"`, headless).
- Browser for the independent denial observation: the OpenCode in-app Chromium browser, same product origin.

## Probe procedure

1. Start the product with `php artisan serve --host=127.0.0.1 --port=8000`.
2. From `probes/2026-11-05-clipboard/`, run `npm install` once, then `npm run probe`.
3. The probe opens `http://127.0.0.1:8000/clipboard-probe.html` and intercepts that URL and `/probe.js` so the probe page is evaluated at the product origin without changing product files.
4. In the granted context, the probe grants `clipboard-read` and `clipboard-write`, writes each sample with `navigator.clipboard.writeText`, reads it back with `navigator.clipboard.readText`, and compares the strings exactly.
5. In the denied context, the probe grants no clipboard permission and repeats the same writes to observe the denial path.
6. The probe exits `0` only when every granted write reads back exactly and every denied write fails with a `NotAllowedError`.

Command result:

```text
exit=0
passed=true
writeSucceeded=true
denialObserved=true
```

## Granted observation

The browser granted `clipboard-read` and `clipboard-write` for `http://127.0.0.1:8000`. Every write resolved and every read-back equaled the written text exactly.

| Sample | Written text | Read-back text | Equal |
| --- | --- | --- | --- |
| `leading-spaces` | `   leading spaces` | `   leading spaces` | yes |
| `trailing-spaces` | `trailing spaces   ` | `trailing spaces   ` | yes |
| `line-breaks` | `line one\nline two\nline three` | `line one\nline two\nline three` | yes |
| `combined` | `  first line\nsecond line\nthird line  ` | `  first line\nsecond line\nthird line  ` | yes |

Raw result for one sample:

```json
{
    "id": "combined",
    "written": "  first line\nsecond line\nthird line  ",
    "readBack": "  first line\nsecond line\nthird line  ",
    "equal": true,
    "success": true,
    "stage": "complete",
    "error": null
}
```

The write required permission. With no clipboard permission granted, `writeText` rejected instead of writing.

## Denied observation

The denied context produced this result for every sample:

```json
{
    "id": "leading-spaces",
    "written": "   leading spaces",
    "readBack": null,
    "equal": false,
    "success": false,
    "stage": "write",
    "error": "NotAllowedError: Failed to execute 'writeText' on 'Clipboard': Write permission denied."
}
```

The same behavior was observed independently in the OpenCode in-app Chromium browser at the product origin. Its permission query returned `clipboard-read: denied` and `clipboard-write: denied`; pressing the probe's `Run leading-spaces` button produced `NotAllowedError: Failed to execute 'writeText' on 'Clipboard': Write permission denied.` The probe rendered `FAIL` and showed the error JSON in its result area.

## Failure behavior

A denied or unavailable write is distinguishable from a successful write by the probe result:

- Success: `stage` is `complete`, `success` is `true`, `readBack` equals `written`, and `error` is `null`.
- Denied write: `stage` is `write`, `success` is `false`, `readBack` is `null`, and `error` starts with `NotAllowedError`.
- Unavailable API: `stage` is `availability`, `success` is `false`, `readBack` is `null`, and `error` is `Clipboard API unavailable`.

The probe therefore fails (`exit 1`) when the Clipboard write is denied or unavailable, and passes only when the read-back text equals the written text exactly.
