const samples = [
    { id: 'leading-spaces', text: '   leading spaces' },
    { id: 'trailing-spaces', text: 'trailing spaces   ' },
    { id: 'line-breaks', text: 'line one\nline two\nline three' },
    { id: 'combined', text: '  first line\nsecond line\nthird line  ' },
];

const results = [];

function record(result) {
    results.push(result);
    document.getElementById('results').textContent = JSON.stringify(results, null, 2);

    const passed = results.length === samples.length && results.every((entry) => entry.success);
    const summary = document.getElementById('summary');
    summary.textContent = passed ? 'PASS' : 'FAIL';
    summary.className = passed ? 'pass' : 'fail';

    return result;
}

async function writeAndRead(text, id = 'ad-hoc') {
    const result = {
        id,
        written: text,
        readBack: null,
        equal: false,
        success: false,
        stage: 'availability',
        error: null,
    };

    if (!navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') {
        result.error = 'Clipboard API unavailable';

        return record(result);
    }

    result.stage = 'write';

    try {
        await navigator.clipboard.writeText(text);
    } catch (error) {
        result.error = `${error.name}: ${error.message}`;

        return record(result);
    }

    result.stage = 'read';

    try {
        result.readBack = await navigator.clipboard.readText();
    } catch (error) {
        result.error = `${error.name}: ${error.message}`;

        return record(result);
    }

    result.stage = 'complete';
    result.equal = result.readBack === text;
    result.success = result.equal;
    result.error = result.equal ? null : 'Read-back text does not equal written text';

    return record(result);
}

async function runSample(sample) {
    return writeAndRead(sample.text, sample.id);
}

async function runAll() {
    for (const sample of samples) {
        await runSample(sample);
    }

    return results;
}

document.getElementById('origin').textContent = window.location.origin;

for (const sample of samples) {
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = `Run ${sample.id}`;
    button.addEventListener('click', () => runSample(sample));
    document.getElementById('buttons').append(button);
}

window.clipboardProbe = { writeAndRead, runSample, runAll, samples, results };
