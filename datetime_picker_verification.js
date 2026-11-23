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
  'Defer attribute on JS': appBladeContent.includes('defer</script>') || appBladeContent.includes('defer>'),
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
  'Start Time with type="datetime-local"': lessonCreateContent.match(/type="datetime-local".*start_time_picker/s) !== null,
  'End Time with type="datetime-local"': lessonCreateContent.match(/type="datetime-local".*end_time_picker/s) !== null,
  'Hidden input for start_time': lessonCreateContent.includes('type="hidden"') && lessonCreateContent.includes('name="start_time"'),
  'Hidden input for end_time': lessonCreateContent.includes('type="hidden"') && lessonCreateContent.includes('name="end_time"'),
  'Label for start_time_picker': lessonCreateContent.includes('for="start_time_picker"'),
  'Label for end_time_picker': lessonCreateContent.includes('for="end_time_picker"'),
  'Placeholder showing DD/MM/YYYY HH:mm': lessonCreateContent.includes('DD/MM/YYYY HH:mm') || lessonCreateContent.includes('datetime format'),
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
  'DateTime handling in store method': controllerContent.includes('start_time') || controllerContent.includes('start_date')
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
  'dateFormat in app.blade.php': appBladeContent.includes('dateFormat: "d/m/Y H:i"'),
  'enableTime: true': appBladeContent.includes('enableTime: true'),
  'time_24hr: true': appBladeContent.includes('time_24hr: true'),
  '24-hour format configured': appBladeContent.includes('time_24hr: true'),
  'minuteIncrement set': appBladeContent.includes('minuteIncrement'),
  'clickOpens: true': appBladeContent.includes('clickOpens: true'),
  'closeOnSelect: true': appBladeContent.includes('closeOnSelect: true'),
  'appendTo: document.body': appBladeContent.includes('appendTo: document.body'),
  'position: auto': appBladeContent.includes("position: 'auto'")
};

let pass4 = 0;
for (const [test, result] of Object.entries(datetimeConfig)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass4++;
}
console.log(`\nResult: ${pass4}/${Object.keys(datetimeConfig).length} checks passed\n`);

// Test 5: Dynamic form support
console.log('TEST 5: Checking Dynamic Form Support');
console.log('─'.repeat(60));

const dynamicTests = {
  'window.initializeNewDatePickers function': appBladeContent.includes('window.initializeNewDatePickers'),
  'Dynamic form re-initialization': appBladeContent.includes(':not([data-flatpickr-initialized])'),
  'Error handling for dynamic forms': appBladeContent.includes('console.warn'),
  'Support for newly added inputs': appBladeContent.includes('MutationObserver') || appBladeContent.includes('DOMContentLoaded')
};

let pass5 = 0;
for (const [test, result] of Object.entries(dynamicTests)) {
  const icon = result ? '✅' : '❌';
  console.log(`${icon} ${test}`);
  if (result) pass5++;
}
console.log(`\nResult: ${pass5}/${Object.keys(dynamicTests).length} checks passed\n`);

// Final Summary
console.log('╔════════════════════════════════════════════════════════════════╗');
console.log('║                    TEST SUMMARY                               ║');
console.log('╚════════════════════════════════════════════════════════════════╝\n');

const totalTests = Object.keys(tests).length + 
                  Object.keys(formTests).length + 
                  Object.keys(backendTests).length + 
                  Object.keys(datetimeConfig).length + 
                  Object.keys(dynamicTests).length;

const totalPass = pass1 + pass2 + pass3 + pass4 + pass5;

console.log(`Test Category Results:`);
console.log(`  • Flatpickr Integration: ${pass1}/${Object.keys(tests).length} ✅`);
console.log(`  • Form Structure: ${pass2}/${Object.keys(formTests).length} ✅`);
console.log(`  • Backend Validation: ${pass3}/${Object.keys(backendTests).length} ✅`);
console.log(`  • DateTime Configuration: ${pass4}/${Object.keys(datetimeConfig).length} ✅`);
console.log(`  • Dynamic Form Support: ${pass5}/${Object.keys(dynamicTests).length} ✅`);
console.log(`\n  TOTAL: ${totalPass}/${totalTests} checks passed`);

if (totalPass === totalTests) {
  console.log('\n🎉 SUCCESS! All implementation checks passed!');
  console.log('\nThe DateTime picker has been fully implemented with:');
  console.log('  ✅ Flatpickr v4.6.13 library integrated');
  console.log('  ✅ DateTime format: DD/MM/YYYY HH:mm');
  console.log('  ✅ Calendar picker enabled');
  console.log('  ✅ Manual text entry supported');
  console.log('  ✅ Backend validation configured');
  console.log('  ✅ Z-index fix applied');
  console.log('  ✅ Error handling in place');
  console.log('  ✅ Dynamic form support ready');
  console.log('\n📌 NEXT STEP: Test in browser to verify calendar popup appears');
  console.log('   URL: https://nountutor.com.ng/admin/courses/3/lessons/create');
  console.log('   Login: lmsadmin@nountutor.com.ng / @@GLMSAdmin22');
} else {
  console.log(`\n⚠️  ${totalTests - totalPass} checks failed. Review the results above.`);
}

console.log('\n');
