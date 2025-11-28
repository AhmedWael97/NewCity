// Performance optimization script
// Run: node optimize-assets.js

const fs = require('fs');
const path = require('path');

console.log('🚀 Optimizing assets for production...\n');

// Configuration
const config = {
    cssDir: path.join(__dirname, 'public', 'css'),
    jsDir: path.join(__dirname, 'public', 'js'),
};

// Clean console output
function cleanConsoleOutput(content) {
    // Remove console.log statements
    return content.replace(/console\.(log|debug|info|warn)\([^)]*\);?/g, '');
}

// Minify CSS (basic)
function minifyCSS(content) {
    return content
        .replace(/\/\*[\s\S]*?\*\//g, '') // Remove comments
        .replace(/\s+/g, ' ') // Replace multiple spaces with single space
        .replace(/\s*([{}:;,])\s*/g, '$1') // Remove spaces around specific chars
        .trim();
}

// Minify JS (basic)
function minifyJS(content) {
    return content
        .replace(/\/\*[\s\S]*?\*\//g, '') // Remove multi-line comments
        .replace(/\/\/.*/g, '') // Remove single-line comments
        .replace(/\s+/g, ' ') // Replace multiple spaces
        .replace(/\s*([{}:;,()=])\s*/g, '$1') // Remove spaces
        .trim();
}

// Process files
function processFiles(dir, processor, extension) {
    if (!fs.existsSync(dir)) {
        console.log(`⚠️  Directory not found: ${dir}`);
        return;
    }

    const files = fs.readdirSync(dir).filter(file => file.endsWith(extension));
    
    files.forEach(file => {
        const filePath = path.join(dir, file);
        const content = fs.readFileSync(filePath, 'utf8');
        const processed = processor(content);
        
        fs.writeFileSync(filePath, processed);
        console.log(`✅ Optimized: ${file}`);
    });
}

// Run optimization
console.log('📦 Optimizing CSS files...');
processFiles(config.cssDir, minifyCSS, '.css');

console.log('\n📦 Optimizing JS files...');
processFiles(config.jsDir, (content) => cleanConsoleOutput(minifyJS(content)), '.js');

console.log('\n✨ Optimization complete!\n');
console.log('📊 Recommendations:');
console.log('   - Enable Gzip compression on server');
console.log('   - Use CDN for static assets');
console.log('   - Enable browser caching');
console.log('   - Use lazy loading for images');
console.log('   - Implement code splitting\n');
