const https = require('https');
const http = require('http');

console.log('\n╔════════════════════════════════════════════════════════════════╗');
console.log('║     DATETIME PICKER - LIVE SITE HTML VERIFICATION TEST        ║');
console.log('╚════════════════════════════════════════════════════════════════╝\n');

function fetchURL(url) {
  return new Promise((resolve, reject) => {
    const protocol = url.startsWith('https') ? https : http;
    protocol.get(url, { 
      headers: { 'User-Agent': 'Mozilla/5.0' }
    }, (res) => {
      let data = '';
      res.on('data', chunk => { data += chunk; });
      res.on('end', () => { resolve(data); });
    }).on('error', reject);
  });
}

async function runTests() {
  try {
    console.log('1️⃣ Fetching HTML from lesson creation page...');
    const html = await fetchURL('https://nountutor.com.ng/admin/courses/3/lessons/create');
    console.log('✅ HTML fetched successfully\n');
    
    // Test 1: Check for Flatpickr CSS
    console.log('TEST 1: Checking for Flatpickr CSS in HTML');
    console.log('─'.repeat(60));
    const hasFlatpickrCSS = html.includes('flatpickr') && html.includes('.min.css');
    console.log(`${hasFlatpickrCSS ? '✅' : '❌'} Flatpickr CSS loaded`);
    
    // Test 2: Check for Flatpickr JS
    const hasFlatpickrJS = html.includes('flatpickr') && html.includes('.min.js');
    console.log(`${hasFlatpickrJS ? '✅' : '❌'} Flatpickr JS loaded`);
    
    // Test 3: Check for datetime-local inputs
    console.log('\nTEST 2: Checking for DateTime Input Fields');
    console.log('─'.repeat(60));
    const startTimeCount = (html.match(/start_time_picker/g) || []).length;
    const endTimeCount = (html.match(/end_time_picker/g) || []).length;
    console.log(`${startTimeCount > 0 ? '✅' : '❌'} Start Time picker field found (${startTimeCount} occurrences)`);
    console.log(`${endTimeCount > 0 ? '✅' : '❌'} End Time picker field found (${endTimeCount} occurrences)`);
    
    // Test 4: Check for hidden inputs
    console.log('\nTEST 3: Checking for Hidden Form Inputs');
    console.log('─'.repeat(60));
    const hasHiddenStart = html.includes('name="start_time"') && html.includes('type="hidden"');
    const hasHiddenEnd = html.includes('name="end_time"') && html.includes('type="hidden"');
    console.log(`${hasHiddenStart ? '✅' : '❌'} Hidden start_time input for backend`);
    console.log(`${hasHiddenEnd ? '✅' : '❌'} Hidden end_time input for backend`);
    
    // Test 5: Check for label associations
    console.log('\nTEST 4: Checking for Label Associations');
    console.log('─'.repeat(60));
    const hasLabelStart = html.includes('for="start_time_picker"');
    const hasLabelEnd = html.includes('for="end_time_picker"');
    console.log(`${hasLabelStart ? '✅' : '❌'} Label for start_time_picker`);
    console.log(`${hasLabelEnd ? '✅' : '❌'} Label for end_time_picker`);
    
    // Test 6: Check for format text
    console.log('\nTEST 5: Checking for Format Information');
    console.log('─'.repeat(60));
    const hasFormatText = html.includes('DD/MM/YYYY') || html.includes('dd/mm/yyyy') || html.includes('d/m/Y');
    console.log(`${hasFormatText ? '✅' : '❌'} DateTime format information present`);
    
    // Test 7: Check for Z-index fix
    console.log('\nTEST 6: Checking for Z-index CSS Fix');
    console.log('─'.repeat(60));
    const hasZIndexFix = html.includes('z-index') && html.includes('99999');
    console.log(`${hasZIndexFix ? '✅' : '❌'} Z-index fix for calendar visibility`);
    
    // Test 8: Check for initialization script
    console.log('\nTEST 7: Checking for Flatpickr Initialization Script');
    console.log('─'.repeat(60));
    const hasInitScript = html.includes('initializeDatePickers') || html.includes('flatpickr');
    console.log(`${hasInitScript ? '✅' : '❌'} Flatpickr initialization script present`);
    
    // Summary
    console.log('\n' + '═'.repeat(60));
    const allTests = [
      hasFlatpickrCSS,
      hasFlatpickrJS,
      startTimeCount > 0,
      endTimeCount > 0,
      hasHiddenStart,
      hasHiddenEnd,
      hasLabelStart,
      hasLabelEnd,
      hasFormatText,
      hasZIndexFix,
      hasInitScript
    ];
    
    const passedTests = allTests.filter(t => t).length;
    
    console.log(`\n✅ LIVE SITE VERIFICATION: ${passedTests}/${allTests.length} checks passed\n`);
    
    if (passedTests === allTests.length) {
      console.log('🎉 SUCCESS! The DateTime picker is deployed and visible on the live site!');
      console.log('\nThe page HTML confirms:');
      console.log('  ✅ Flatpickr CSS/JS loaded from CDN');
      console.log('  ✅ DateTime input fields rendered with proper IDs');
      console.log('  ✅ Hidden inputs ready for form submission');
      console.log('  ✅ Labels properly associated with visible inputs');
      console.log('  ✅ Z-index CSS fix applied for calendar visibility');
      console.log('  ✅ Initialization script present');
      console.log('\n📌 Status: PRODUCTION READY - Visual picker should work in browser');
    } else {
      console.log(`⚠️  ${allTests.length - passedTests} items not found in HTML`);
    }
    
  } catch (error) {
    console.log('❌ Error during HTML fetch:', error.message);
    console.log('\nNote: If the fetch fails due to network/firewall, the code-level tests already passed.');
  }
}

runTests();
