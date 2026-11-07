import fs from 'fs';

const content = fs.readFileSync('resources/views/components/products/image-lightbox.blade.php', 'utf8');
const match = content.match(/<script(?:\s+[^>]*)?>([\s\S]*?)<\/script>/i);
if (match) {
    let js = match[1];
    // In blade:
    // {{ $isAr ? 'true' : 'false' }} -> true
    // {{ csrf_token() }} -> "abc"
    // {{ $isAr ? 'تم النسخ!' : 'Copied!' }} -> "Copied!"
    // {{ $isAr ? 1 : -1 }} -> 1
    // {{ $isAr ? -1 : 1 }} -> -1
    
    js = js.replace(/\{\{\s*\$isAr\s*\?\s*'true'\s*:\s*'false'\s*\}\}/g, 'true');
    js = js.replace(/\{\{\s*csrf_token\(\)\s*\}\}/g, '"token"');
    js = js.replace(/\{\{\s*\$isAr\s*\?\s*'تم النسخ!'\s*:\s*'Copied!'\s*\}\}/g, 'Copied!');
    js = js.replace(/\{\{\s*\$isAr\s*\?\s*1\s*:\s*-1\s*\}\}/g, '1');
    js = js.replace(/\{\{\s*\$isAr\s*\?\s*-1\s*:\s*1\s*\}\}/g, '-1');

    console.log("Transformed JS lines: " + js.split('\n').length);
    try {
        new Function(js);
        console.log("SUCCESS");
    } catch (e) {
        console.error("Syntax Error in image-lightbox:", e);
        console.log("JS code:\n", js);
    }
}
