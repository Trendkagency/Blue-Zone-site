import fs from 'fs';

const html = fs.readFileSync('storage/downloaded_edit.html', 'utf8');
const lines = html.split('\n');
console.log('Total lines in downloaded_edit.html:', lines.length);

if (lines.length > 100) {
    console.log('Lines 8730 to 8760:');
    for (let i = Math.max(0, 8730 - 1); i < Math.min(lines.length, 8760); i++) {
        console.log(`${i + 1}: ${lines[i]}`);
    }
} else {
    console.log('Short output (redirect?):', html.substring(0, 500));
}
