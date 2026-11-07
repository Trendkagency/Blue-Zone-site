const fs = require('fs');

function testBladeFile(path) {
    console.log("=== Testing " + path + " ===");
    const content = fs.readFileSync(path, 'utf8');
    const regex = /<script(?:\s+[^>]*)?>([\s\S]*?)<\/script>/gi;
    let match;
    let idx = 0;
    while ((match = regex.exec(content)) !== null) {
        idx++;
        let js = match[1];
        // replace blade expressions {{ ... }} and {!! ... !!} and @json(...) with dummy values
        js = js.replace(/@json\([^)]*\)/g, '{}');
        js = js.replace(/\{\{\s*[\s\S]*?\s*\}\}/g, '""');
        js = js.replace(/\{!!\s*[\s\S]*?\s*!!\}/g, '""');
        js = js.replace(/@php[\s\S]*?@endphp/g, '');
        js = js.replace(/@if\([^)]*\)/g, 'if (true) {');
        js = js.replace(/@elseif\([^)]*\)/g, '} else if (true) {');
        js = js.replace(/@else/g, '} else {');
        js = js.replace(/@endif/g, '}');

        try {
            new Function(js);
            console.log(`Script #${idx}: OK (${js.length} chars)`);
        } catch (err) {
            console.error(`Script #${idx} ERROR:`, err.message);
            // Print error location if available
            const lines = js.split('\n');
            console.error('Lines around error:');
            // Check where the error might be
            for (let i = 0; i < Math.min(lines.length, 50); i++) {
                // ...
            }
        }
    }
}

testBladeFile('resources/views/components/products/image-lightbox.blade.php');
testBladeFile('resources/views/admin/products/edit.blade.php');
testBladeFile('resources/views/admin/products/create.blade.php');
