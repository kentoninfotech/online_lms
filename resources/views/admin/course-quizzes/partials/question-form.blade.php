<style>
.question-form-container .form-control,
.question-form-container .form-select {
    border-radius: 0.5rem;
}

.feature-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
}

#answersContainer .input-group {
    margin-bottom: 0.75rem;
}

#answersContainer .input-group .form-control {
    border-right: 0;
}

#answersContainer .input-group-text {
    border-left: 0;
    background-color: transparent;
}
</style>

<div class="question-form-container">
    <!-- Question Text -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Question Text <span class="feature-badge bg-danger text-white">Required</span></label>
        <textarea name="question" rows="4" class="form-control @error('question') is-invalid @enderror" 
            placeholder="Enter your question here. Be clear and concise." required>{{ old('question', $question->question ?? '') }}</textarea>
        <small class="form-text text-muted d-block mt-2">
            <i class="bi bi-info-circle me-1"></i>Provide a clear, well-formed question that students can understand.
        </small>
        @error('question')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Question Type Selection -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Question Type <span class="feature-badge bg-danger text-white">Required</span></label>
        <select name="question_type" id="questionType" class="form-select @error('question_type') is-invalid @enderror" required onchange="updateAnswerOptions()">
            <option value="">-- Select Question Type --</option>
            <option value="multiple_choice" {{ old('question_type', $question->question_type ?? '') === 'multiple_choice' ? 'selected' : '' }}>
                <i class="bi bi-circle-fill"></i> Multiple Choice (Single Answer)
            </option>
            <option value="multiple_answer" {{ old('question_type', $question->question_type ?? '') === 'multiple_answer' ? 'selected' : '' }}>
                <i class="bi bi-square-fill"></i> Multiple Answer (Multiple Correct)
            </option>
            <option value="true_false" {{ old('question_type', $question->question_type ?? '') === 'true_false' ? 'selected' : '' }}>
                <i class="bi bi-toggle-on"></i> True / False
            </option>
            <option value="yes_no" {{ old('question_type', $question->question_type ?? '') === 'yes_no' ? 'selected' : '' }}>
                <i class="bi bi-hand-thumbs-up"></i> Yes / No
            </option>
            <option value="short_answer" {{ old('question_type', $question->question_type ?? '') === 'short_answer' ? 'selected' : '' }}>
                <i class="bi bi-type"></i> Short Answer
            </option>
        </select>
        @error('question_type')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Points -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Points <span class="feature-badge bg-danger text-white">Required</span></label>
        <input type="number" name="points" class="form-control @error('points') is-invalid @enderror" 
            placeholder="e.g., 5" value="{{ old('points', $question->points ?? 1) }}" min="1" max="100" required>
        <small class="form-text text-muted d-block mt-2">
            <i class="bi bi-info-circle me-1"></i>Points awarded for answering this question correctly.
        </small>
        @error('points')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Difficulty Level (Optional) -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Difficulty Level</label>
        <select name="difficulty_level" class="form-select">
            <option value="easy" {{ old('difficulty_level', $question->difficulty_level ?? '') === 'easy' ? 'selected' : '' }}>Easy</option>
            <option value="medium" {{ old('difficulty_level', $question->difficulty_level ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="hard" {{ old('difficulty_level', $question->difficulty_level ?? '') === 'hard' ? 'selected' : '' }}>Hard</option>
        </select>
    </div>

    <hr class="my-4">

    <!-- Answer Options (for multiple choice/multiple answer) -->
    <div id="answerOptions" style="display: none;">
        <div class="mb-4">
            <label class="form-label fw-semibold d-flex align-items-center">
                <span>Answer Options</span>
                <span class="feature-badge bg-info text-white ms-2">At least 2 options required</span>
            </label>
            <div id="answersContainer" class="mb-3">
                @if($question && in_array($question->question_type, ['multiple_choice', 'multiple_answer']))
                    @foreach($question->answers as $index => $answer)
                    <div class="input-group mb-2">
                        <input type="text" name="answers[]" class="form-control" value="{{ $answer->answer_text }}" placeholder="Enter answer option">
                        <div class="input-group-text">
                            <input type="checkbox" name="correct_answers[]" value="{{ $answer->id }}" 
                                {{ is_array($question->correct_answer) && in_array($answer->id, $question->correct_answer) ? 'checked' : '' }}
                                class="form-check-input mt-0" title="Mark as correct">
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    @endforeach
                @else
                    <div class="input-group mb-2">
                        <input type="text" name="answers[]" class="form-control" placeholder="Enter first answer">
                        <div class="input-group-text">
                            <input type="checkbox" name="correct_answers[]" value="0" class="form-check-input mt-0">
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)"><i class="bi bi-trash"></i></button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" name="answers[]" class="form-control" placeholder="Enter second answer">
                        <div class="input-group-text">
                            <input type="checkbox" name="correct_answers[]" value="1" class="form-check-input mt-0">
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)"><i class="bi bi-trash"></i></button>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAnswer()">
                <i class="bi bi-plus-circle me-1"></i> Add Answer Option
            </button>
            <small class="d-block mt-2 text-muted">
                <i class="bi bi-info-circle me-1"></i>Check the box(es) next to the correct answer(s). For single-choice questions, check only one.
            </small>
        </div>
    </div>

    <!-- True/False Options -->
    <div id="trueFalseOptions" style="display: none;">
        <div class="mb-4">
            <label class="form-label fw-semibold">Correct Answer <span class="feature-badge bg-danger text-white">Required</span></label>
            <div class="btn-group d-block mb-3" role="group">
                <input type="radio" class="btn-check" name="correct_answer_tf" id="optTrue" value="true" 
                    {{ old('correct_answer_tf', $question->correct_answer ?? '') === 'true' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="optTrue">
                    <i class="bi bi-check-circle me-1"></i>True
                </label>

                <input type="radio" class="btn-check" name="correct_answer_tf" id="optFalse" value="false"
                    {{ old('correct_answer_tf', $question->correct_answer ?? '') === 'false' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="optFalse">
                    <i class="bi bi-x-circle me-1"></i>False
                </label>
            </div>
        </div>
    </div>

    <!-- Yes/No Options -->
    <div id="yesNoOptions" style="display: none;">
        <div class="mb-4">
            <label class="form-label fw-semibold">Correct Answer <span class="feature-badge bg-danger text-white">Required</span></label>
            <div class="btn-group d-block mb-3" role="group">
                <input type="radio" class="btn-check" name="correct_answer_yn" id="optYes" value="yes"
                    {{ old('correct_answer_yn', $question->correct_answer ?? '') === 'yes' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="optYes">
                    <i class="bi bi-hand-thumbs-up me-1"></i>Yes
                </label>

                <input type="radio" class="btn-check" name="correct_answer_yn" id="optNo" value="no"
                    {{ old('correct_answer_yn', $question->correct_answer ?? '') === 'no' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="optNo">
                    <i class="bi bi-hand-thumbs-down me-1"></i>No
                </label>
            </div>
        </div>
    </div>

    <!-- Short Answer -->
    <div id="shortAnswerOptions" style="display: none;">
        <div class="mb-4">
            <label class="form-label fw-semibold d-flex align-items-center">
                <span>Correct Answer(s)</span>
                <span class="feature-badge bg-info text-white ms-2">At least 1 required</span>
            </label>
            <p class="form-text text-muted small mb-3">
                <i class="bi bi-lightbulb me-1"></i>Add multiple acceptable answers. Matching is case-insensitive by default.
            </p>
            <div id="shortAnswerContainer">
                @if($question && $question->question_type === 'short_answer')
                    @if(is_array($question->correct_answer))
                        @foreach($question->correct_answer as $answer)
                        <div class="input-group mb-2">
                            <input type="text" name="short_answers[]" class="form-control" value="{{ $answer }}" placeholder="Enter acceptable answer">
                            <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2">
                            <input type="text" name="short_answers[]" class="form-control" value="{{ $question->correct_answer }}" placeholder="Enter acceptable answer">
                            <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="input-group mb-2">
                        <input type="text" name="short_answers[]" class="form-control" placeholder="Enter first acceptable answer">
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addShortAnswer()">
                <i class="bi bi-plus-circle me-1"></i> Add Answer
            </button>
            <small class="d-block mt-2 text-muted">
                <i class="bi bi-info-circle me-1"></i>Students' answers are compared case-insensitively.
            </small>
        </div>
    </div>
</div>

<script>
function updateAnswerOptions() {
    const type = document.getElementById('questionType').value;
    document.getElementById('answerOptions').style.display = ['multiple_choice', 'multiple_answer'].includes(type) ? 'block' : 'none';
    document.getElementById('trueFalseOptions').style.display = type === 'true_false' ? 'block' : 'none';
    document.getElementById('yesNoOptions').style.display = type === 'yes_no' ? 'block' : 'none';
    document.getElementById('shortAnswerOptions').style.display = type === 'short_answer' ? 'block' : 'none';
}

function addAnswer() {
    const container = document.getElementById('answersContainer');
    const index = container.children.length;
    const html = `
        <div class="input-group mb-2">
            <input type="text" name="answers[]" class="form-control" placeholder="Enter answer option">
            <div class="input-group-text">
                <input type="checkbox" name="correct_answers[]" value="${index}" class="form-check-input mt-0">
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removeAnswer(btn) {
    btn.parentElement.remove();
}

function addShortAnswer() {
    const container = document.getElementById('shortAnswerContainer');
    const html = `
        <div class="input-group mb-2">
            <input type="text" name="short_answers[]" class="form-control" placeholder="Enter acceptable answer">
            <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateAnswerOptions);
</script>
