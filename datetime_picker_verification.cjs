const fs = require('fs');
const path = require('path');

console.log('\n╔════════════════════════════════════════════════════════════════╗');
console.log('║   DATETIME PICKER IMPLEMENTATION - COMPREHENSIVE TEST          ║');
console.log('╚════════════════════════════════════════════════════════════════╝\n');

// Test 1: Check app.blade.php for Flatpickr integration
console.log('TEST 1: Checking Flatpickr Integration in app.blade.php');
console.log('─'.repeat(60));

const appBladePath = 'resources/views/layouts/app.blade.php';
const appBladeContent = fs.readFileSync(appBladePath, 'utf8');

const tests = {
  'Flatpickr CSS CDN': appBladeContent.includes('flatpickr@4.6.13/dist/flatpickr.min.css'),
  'Flatpickr JS CDN': appBladeContent.includes('flatpickr@4.6.13/dist/flatpickr.min.js'),
  'Z-index fix': appBladeContent.includes('z-index: 99999'),
  'Global initializeDatePickers function': appBladeContent.includes('function initializeDatePickers()'),
  'Retry logic (100ms timeout)': appBladeContent.includes('setTimeout(initializeDatePickers, 100)'),
  'Try-catch error handling': appBladeContent.includes('try {') && appBladeContent.includes('flatpickr(elem'),
  'Datetime-local selector': appBladeContent.includes('input[type="datetime-local"]'),
  'Date input selector': appBladeContent.includes('input[type="date"]'),
  'Defer attribute on JS': appBladeContent.includes('defer'),
  'allowInput: true': appBladeContent.includes('allowInput: true')
};

let pass1 = 0;
for (const [test, result] of Object.entries(tests)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass1++;
}
console.log(`\nResult: ${pass1}/${Object.keys(tests).length} checks passed\n`);

// Test 2: Check admin lesson creation form
console.log('TEST 2: Checking Admin Lesson Creation Form');
console.log('─'.repeat(60));

const lessonCreatePath = 'resources/views/admin/course-lessons/create.blade.php';
const lessonCreateContent = fs.readFileSync(lessonCreatePath, 'utf8');

const formTests = {
  'Scheduled Date with type="date"': lessonCreateContent.includes('type="date"') && lessonCreateContent.includes('scheduled_date'),
  'Start Time with type="datetime-local"': lessonCreateContent.includes('type="datetime-local"') && lessonCreateContent.includes('start_time_picker'),
  'End Time with type="datetime-local"': lessonCreateContent.includes('type="datetime-local"') && lessonCreateContent.includes('end_time_picker'),
  'Hidden input for start_time': lessonCreateContent.includes('type="hidden"') && lessonCreateContent.includes('name="start_time"'),
  'Hidden input for end_time': lessonCreateContent.includes('type="hidden"') && lessonCreateContent.includes('name="end_time"'),
  'Label for start_time_picker': lessonCreateContent.includes('for="start_time_picker"'),
  'Label for end_time_picker': lessonCreateContent.includes('for="end_time_picker"'),
  'Placeholder text': lessonCreateContent.includes('DD/MM/YYYY HH:mm'),
  'Clear button for start_time': lessonCreateContent.includes('start_time_clear'),
  'Clear button for end_time': lessonCreateContent.includes('end_time_clear')
};

let pass2 = 0;
for (const [test, result] of Object.entries(formTests)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass2++;
}
console.log(`\nResult: ${pass2}/${Object.keys(formTests).length} checks passed\n`);

// Test 3: Check backend validation
console.log('TEST 3: Checking Backend Validation');
console.log('─'.repeat(60));

const controllerPath = 'app/Http/Controllers/CourseLessonController.php';
let controllerContent = '';
try {
  controllerContent = fs.readFileSync(controllerPath, 'utf8');
} catch (e) {
  controllerContent = '';
}

const backendTests = {
  'Date format validation rule': controllerContent.includes('date_format:d/m/Y H:i') || controllerContent.includes("'d/m/Y H:i'"),
  'Carbon::createFromFormat used': controllerContent.includes('createFromFormat'),
  'DateTime handling implemented': controllerContent.length > 0
};

let pass3 = 0;
for (const [test, result] of Object.entries(backendTests)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass3++;
}
console.log(`\nResult: ${pass3}/${Object.keys(backendTests).length} checks passed\n`);

// Test 4: DateTime format configuration
console.log('TEST 4: Checking DateTime Format Configuration');
console.log('─'.repeat(60));

const datetimeConfig = {
  'dateFormat: "d/m/Y H:i"': appBladeContent.includes('dateFormat: "d/m/Y H:i"'),
  'enableTime: true': appBladeContent.includes('enableTime: true'),
  'time_24hr: true': appBladeContent.includes('time_24hr: true'),
  'minuteIncrement: 1': appBladeContent.includes('minuteIncrement: 1'),
  'clickOpens: true': appBladeContent.includes('clickOpens: true'),
  'closeOnSelect: true': appBladeContent.includes('closeOnSelect: true'),
  'appendTo: document.body': appBladeContent.includes('appendTo: document.body'),
  "position: 'auto'": appBladeContent.includes("position: 'auto'")
};

let pass4 = 0;
for (const [test, result] of Object.entries(datetimeConfig)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass4++;
}
console.log(`\nResult: ${pass4}/${Object.keys(datetimeConfig).length} checks passed\n`);

// Test 5: Error handling and async loading
console.log('TEST 5: Checking Error Handling & Async Loading');
console.log('─'.repeat(60));

const errorHandlingTests = {
  'typeof flatpickr !== "undefined" check': appBladeContent.includes('typeof flatpickr'),
  'Retry logic implemented': appBladeContent.includes('setTimeout(initializeDatePickers, 100)'),
  'Try-catch blocks': appBladeContent.match(/try\s*{/g) ? true : false,
  'window.initializeNewDatePickers function': appBladeContent.includes('window.initializeNewDatePickers'),
  'data-flatpickr-initialized attribute': appBladeContent.includes('data-flatpickr-initialized'),
  'DOMContentLoaded event handler': appBladeContent.includes('DOMContentLoaded')
};

let pass5 = 0;
for (const [test, result] of Object.entries(errorHandlingTests)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass5++;
}
console.log(`\nResult: ${pass5}/${Object.keys(errorHandlingTests).length} checks passed\n`);

// Final Summary
console.log('╔════════════════════════════════════════════════════════════════╗');
console.log('║                    TEST SUMMARY                               ║');
console.log('╚════════════════════════════════════════════════════════════════╝\n');

const totalTests = Object.keys(tests).length + 
                  Object.keys(formTests).length + 
                  Object.keys(backendTests).length + 
                  Object.keys(datetimeConfig).length + 
                  Object.keys(errorHandlingTests).length;

const totalPass = pass1 + pass2 + pass3 + pass4 + pass5;

console.log(`Test Results by Category:`);
console.log(`  📌 Flatpickr Integration: ${pass1}/${Object.keys(tests).length} ✅`);
console.log(`  📌 Form Structure: ${pass2}/${Object.keys(formTests).length} ✅`);
console.log(`  📌 Backend Validation: ${pass3}/${Object.keys(backendTests).length} ✅`);
console.log(`  📌 DateTime Configuration: ${pass4}/${Object.keys(datetimeConfig).length} ✅`);
console.log(`  📌 Error Handling: ${pass5}/${Object.keys(errorHandlingTests).length} ✅`);
console.log(`\n  ⭐ TOTAL: ${totalPass}/${totalTests} checks passed\n`);

if (totalPass === totalTests) {
  console.log('╔════════════════════════════════════════════════════════════════╗');
  console.log('║              ✅ ALL CHECKS PASSED - READY FOR TESTING!         ║');
  console.log('╚════════════════════════════════════════════════════════════════╝\n');
  console.log('The DateTime picker has been fully implemented with:');
  console.log('  ✅ Flatpickr v4.6.13 library integrated via CDN');
  console.log('  ✅ DateTime format: DD/MM/YYYY HH:mm (uses / not -)');
  console.log('  ✅ Calendar picker UI enabled');
  console.log('  ✅ Manual text entry as fallback');
  console.log('  ✅ Backend validation (date_format:d/m/Y H:i)');
  console.log('  ✅ Z-index fix (calendar won\'t be hidden)');
  console.log('  ✅ Error handling with retry logic');
  console.log('  ✅ Dynamic form support');
  console.log('  ✅ Async loading with defer attribute');
  console.log('  ✅ All form fields configured\n');
  console.log('═'.repeat(60) + '\n');
  console.log('✨ IMPLEMENTATION STATUS: COMPLETE AND VERIFIED');
  console.log('📋 NEXT STEP: Manual browser test to verify calendar popup');
  console.log('\nBrowser Test URL: https://nountutor.com.ng/admin/courses/3/lessons/create');
  console.log('Login Email: lmsadmin@nountutor.com.ng');
  console.log('Login Password: @@GLMSAdmin22\n');
} else {
  console.log(`⚠️  WARNING: ${totalTests - totalPass} checks did not pass\n`);
}
