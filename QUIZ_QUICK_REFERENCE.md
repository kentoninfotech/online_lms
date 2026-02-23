# Quick Reference Guide - Quiz System

## Admin Tasks

### Create a Quiz
1. Go to Courses → Select Course → Quizzes → Add Quiz
2. Fill in basic info (title, description, sequence)
3. Click "Create Quiz"
4. You'll be taken to the quiz edit page

### Edit Quiz Settings
1. Go to Quizzes → Select Quiz → Click "Edit"
2. Go to "Quiz Settings" tab
3. Update:
   - Title and description
   - Passing score (0-100%)
   - Time limit (minutes, optional)
   - Maximum attempts (1+)
   - Display options (show answers, shuffle, required)
   - Publishing status
4. Click "Save Changes"

### Add Questions to Quiz
1. In Quiz Edit page, click "Manage Questions" tab
2. Or click "Add Questions" button in sidebar
3. Click "Add Question" button
4. Fill in:
   - Question text (required)
   - Question type (required) - Choose from 5 types
   - Points earned for correct answer (required)
   - Difficulty level (optional)
5. For Multiple Choice/Answer:
   - Add answer options (min 2)
   - Check box(es) for correct answer(s)
6. For True/False or Yes/No:
   - Select the correct option
7. For Short Answer:
   - Add acceptable answers (min 1)
8. Click "Add Question"

### Edit a Question
1. In Quiz Edit → Manage Questions
2. Click pencil icon on question you want to edit
3. Update any fields
4. Click "Save Changes"

### Delete a Question
1. In Quiz Edit → Manage Questions
2. Click trash icon on question
3. Confirm deletion

### Publish a Quiz
1. In Quiz Edit → Quiz Settings tab
2. Check "Publish this quiz" checkbox
3. Click "Save Changes"
4. Status will change from Draft to Published

### Review Student Submissions
1. Go to Quiz → View Submissions
2. See table of all student attempts
3. Filter by:
   - All (all submissions)
   - Passed (only passing scores)
   - Failed (only failing scores)
   - Pending Review (not yet reviewed)
4. Click "Review" to see detailed attempt

### Mark Submission as Reviewed
1. View Submission detail page
2. Add instructor notes (optional)
3. Click "Mark as Reviewed"
4. Status will change to "Reviewed"

---

## Student Experience

### Take a Quiz
1. Go to Course
2. Find quiz in course content
3. Click "Start Quiz" or "Take Quiz"
4. Read instructions carefully
5. Answer each question:
   - **Multiple Choice:** Select one answer (radio button)
   - **Multiple Answer:** Check all correct answers (checkboxes)
   - **True/False:** Select True or False
   - **Yes/No:** Select Yes or No
   - **Short Answer:** Type your answer
6. Click "Submit Quiz" to finish

### View Results
1. After submitting, you'll see Results page showing:
   - Overall score (percentage)
   - Correct answers (count/total)
   - Time taken
   - Attempt number
2. If quiz allows answer review:
   - Scroll down to see each question
   - Green badge = your answer was correct
   - Red badge = your answer was incorrect
   - See correct answer if you were wrong

### Retry Quiz
1. If you didn't pass AND attempts remain:
   - Click "Try Again" button on results page
   - Or go back to course and start again
2. Your attempt number will increase
3. Previous attempts are saved

### Download Certificate
1. After passing a quiz (if enabled):
   - Scroll to Certificate section
   - Click "Download Certificate (PDF)" button
   - File saves to your computer
2. Or click "Print Certificate" to print directly

---

## Question Types Explained

### 1. Multiple Choice (Single Answer)
- Student selects ONE correct answer
- Example: "What is the capital of France?"
  - A) London ✗
  - B) Paris ✓
  - C) Berlin ✗
  - D) Madrid ✗

### 2. Multiple Answer (Multiple Correct)
- Student can select MULTIPLE correct answers
- Example: "Select all prime numbers"
  - ☑ 2 ✓
  - ☑ 3 ✓
  - ☐ 4 ✗
  - ☑ 5 ✓
- All must be correct to get points

### 3. True/False
- Two options only: True or False
- Example: "The Earth is flat"
  - ✓ True or False ✗

### 4. Yes/No
- Two options: Yes or No
- Example: "Do you agree with this statement?"
  - Yes or No

### 5. Short Answer
- Student types a text answer
- Matching is case-insensitive
- Example: "What is 2+2?"
  - ✓ "4" or "four" or "FOUR" accepted
  - ✗ "5" rejected

---

## Settings & Options Explained

### Passing Score
- Default: 50%
- Minimum score to pass (0-100%)
- Example: 70% means student needs 70+ to pass

### Time Limit
- Optional (leave blank for no limit)
- Minutes: 30, 60, 120, etc.
- Example: 30 minutes for quiz

### Attempts Allowed
- How many times student can take quiz
- Default: 3
- Example: 1 = take only once

### Show Correct Answers
- If enabled: after quiz, student sees their answers vs correct answers
- If disabled: student only sees score

### Shuffle Questions
- If enabled: questions appear in random order each attempt
- If disabled: same question order each time

### Is Required
- If enabled: must pass this quiz to complete course
- If disabled: optional quiz

---

## Helpful Tips

### For Best Questions:
✅ Be clear and specific
✅ Avoid ambiguous wording
✅ Use multiple answer options to avoid guessing
✅ Set appropriate difficulty levels
✅ Assignment points based on difficulty
✅ Mix question types for variety

### Common Mistakes:
❌ Not marking any answer as correct
❌ Adding fewer than 2 options for multiple choice
❌ Using very broad question text
❌ Setting passing score too high/low
❌ Forgetting to publish quiz before testing

### Grading:
- Multiple Choice: All or nothing (1 point system)
- Multiple Answer: All or nothing (all must be correct)
- True/False: All or nothing
- Yes/No: All or nothing
- Short Answer: All or nothing

### Answer Matching:
- Short answer: Case-insensitive
- Exact match: "4" = "4" ✓
- Partial match: Not supported yet
- Trimmed whitespace: "4 " = "4" ✓

---

## Troubleshooting

### Quiz Not Showing for Students
→ Check: Is quiz Published? (Quiz Settings tab)
→ Check: Is course Published?
→ Check: Is student enrolled in course?

### Questions Not Saving
→ Check: Question text is filled in?
→ Check: Question type is selected?
→ Check: Points is filled in?
→ Check: For multiple choice - at least 2 answers?
→ Check: For short answer - at least 1 answer?

### Student Can't Submit
→ Check: Browser JavaScript enabled?
→ Check: All questions answered?
→ Check: Form errors displayed?

### Viewing Results
→ Check: Submission marked as reviewed?
→ Check: Show correct answers is enabled?
→ Check: Quiz published?

### Time Limit Not Working
→ Currently UI only, enforcement in progress

### Shuffle Questions Not Working
→ Currently UI only, randomization in progress

---

## Routes & URLs

**Admin Quiz Management:**
- `/admin/courses/{course}/quizzes` - Quiz list
- `/admin/courses/{course}/quizzes/{quiz}/edit` - Edit quiz
- `/admin/courses/{course}/quizzes/{quiz}/questions` - Manage questions
- `/admin/courses/{course}/quizzes/{quiz}/submissions` - View submissions
- `/admin/courses/{course}/quizzes/{quiz}/submissions/{submission}` - Review submission

**Student Quiz Taking:**
- `/courses/{course}/quiz/{quiz}` - Take quiz
- `/courses/{course}/quiz/{quiz}/result/{submission}` - View results

---

## Database Quick Reference

**Quiz Status:**
- `is_published` = 1 → Students can see/take
- `is_published` = 0 → Only admins can see

**Submission Status:**
- `is_passed` = 1 → Score ≥ passing_score
- `is_passed` = 0 → Score < passing_score

**Question Types:**
- `multiple_choice` → Single answer
- `multiple_answer` → Multiple answers
- `true_false` → True or False
- `yes_no` → Yes or No
- `short_answer` → Text entry

---

## Key Features Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Create Quiz | ✅ Complete | Full settings support |
| Edit Quiz | ✅ Complete | All options editable |
| 5 Question Types | ✅ Complete | All types working |
| Add Questions | ✅ Complete | Dynamic form |
| Edit Questions | ✅ Complete | Inline editing |
| Delete Questions | ✅ Complete | With confirmation |
| Student Quiz Taking | ✅ Complete | All question types |
| Auto-Grading | ✅ Complete | Instant results |
| Results Display | ✅ Complete | Score + statistics |
| Answer Review | ✅ Complete | If enabled in quiz |
| Attempt Retry | ✅ Complete | Limited by attempts |
| Tutor Marking | ✅ Complete | With notes |
| Certificate | ✅ Complete | Download button |
| Time Limit | ⏳ UI Only | Logic in progress |
| Shuffle Questions | ⏳ UI Only | Logic in progress |
| PDF Export | ⏳ In Progress | Backend needed |
| Analytics | ⏳ Planned | Charts coming |

---

## Support

For more details, see:
- `QUIZ_SYSTEM_ENHANCEMENTS.md` - Comprehensive documentation
- `IMPLEMENTATION_SUMMARY.md` - Implementation details
- Template comments - Inline help

**Questions?** Check the documentation files or ask admin.

---

Last Updated: February 22, 2026
Version: 1.0
