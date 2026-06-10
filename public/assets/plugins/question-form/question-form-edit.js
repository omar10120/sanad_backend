$(document).ready(function() {
    const config = window.questionEditConfig || {};
    const routes = config.routes || {};
    const initial = config.initial || {};
    const labels = config.labels || {};
    const subjectSelect = $('#subject');
    const lessonSelect = $('#lesson');
    const questionGroupSelect = $('#question-group');
    const tagsSelect = $('#questionTags');
    const clearHintBtn = $('#clearHintBtn');
    const clearTagsBtn = $('#clearTagsBtn');
    const clearHintInput = $('#clear_hint');
    const clearTagsInput = $('#clear_tags');

    const buildUrl = (template, value) => {
        if (!template || !value) return '';
        return template.replace(':subjectId', value).replace(':lessonId', value);
    };

    // Initialize Quill for Question Text
    const quill = new Quill('#questionTextEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                ['formula', 'link'],
                [{ 'direction': 'rtl' }], // text direction
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Enter the question text here...'
    });

    quill.on('text-change', function() {
        $('#questionText').val(JSON.stringify(quill.getContents())); // Store delta as JSON
    });

    // Initialize Quill for Hint
    const hintQuill = new Quill('#hintEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                ['formula', 'link'],
                [{ 'direction': 'rtl' }], // text direction
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Enter a hint to help the user...'
    });

    hintQuill.on('text-change', function() {
        $('#hint').val(JSON.stringify(hintQuill.getContents())); // Store delta as JSON
        if (clearHintInput.length) {
            clearHintInput.val('0');
        }
    });

    let correctAnswerQuill;

    // Load existing data into Quill editors
    const questionText = JSON.parse($('#questionText').val() || '{}');
    if (quill && questionText) {
        quill.setContents(questionText);
    }

    const hintText = JSON.parse($('#hint').val() || '{}');
    if (hintQuill && hintText) {
        hintQuill.setContents(hintText);
    }

    // Clear hint content
    if (clearHintBtn.length) {
        clearHintBtn.on('click', function() {
            hintQuill.setContents([{ insert: '\n' }]);
            $('#hint').val('');
            if (clearHintInput.length) {
                clearHintInput.val('1');
            }
        });
    }

    // Clear all tags selection
    if (clearTagsBtn.length && tagsSelect.length) {
        clearTagsBtn.on('click', function() {
            tagsSelect.val(null).trigger('change');
            if (clearTagsInput.length) {
                clearTagsInput.val('1');
            }
        });

        tagsSelect.on('change', function() {
            if (clearTagsInput.length) {
                const hasSelection = ($(this).val() || []).length > 0;
                clearTagsInput.val(hasSelection ? '0' : '1');
            }
        });
    }

    function loadQuestionGroups(lessonId, keepCurrent = false) {
        const url = buildUrl(routes.questionGroupsByLesson, lessonId);
        if (!url) return;

        questionGroupSelect.prop('disabled', true);
        $.get(url, function(response) {
            const groups = response.data || [];
            const newGroupLabel = questionGroupSelect.data('new-group-label') || labels.newGroup || 'New question group';

            questionGroupSelect.empty();
            questionGroupSelect.append(new Option(newGroupLabel, -1));

            groups.forEach(group => {
                questionGroupSelect.append(
                    new Option(`${group.id} - ${group.name}`, group.id)
                );
            });

            let targetGroupId = keepCurrent ? initial.questionGroupId : null;
            if (targetGroupId) {
                const exists = groups.some(group => String(group.id) === String(targetGroupId));
                if (!exists) {
                    targetGroupId = null;
                }
            }

            questionGroupSelect.val(targetGroupId ? String(targetGroupId) : '-1').trigger('change');
            questionGroupSelect.prop('disabled', false);
        });
    }

    function loadLessons(subjectId) {
        const url = buildUrl(routes.lessonsBySubject, subjectId);
        if (!url) return;

        lessonSelect.prop('disabled', true);
        $.get(url, function(response) {
            const lessons = response.data || [];
            const placeholder = labels.selectLesson || 'Select lesson';

            lessonSelect.empty();
            lessonSelect.append(new Option(placeholder, '', true, false));

            lessons.forEach(lesson => {
                lessonSelect.append(new Option(lesson.title, lesson.id));
            });

            let selectedLessonId = null;
            const initialLessonId = initial.lessonId;
            if (subjectId && String(subjectId) === String(initial.subjectId)) {
                selectedLessonId = initialLessonId;
            } else if (lessons.length > 0) {
                selectedLessonId = lessons[0].id;
            }

            if (selectedLessonId) {
                lessonSelect.val(String(selectedLessonId));
            }

            lessonSelect.prop('disabled', false).trigger('change');
        });
    }

    function loadTags(subjectId) {
        const url = buildUrl(routes.tagsBySubject, subjectId);
        if (!url || !tagsSelect.length) return;

        const currentSelection = tagsSelect.val() || [];
        $.get(url, function(response) {
            const tags = response.data || [];
            tagsSelect.empty();

            tags.forEach(tag => {
                const option = new Option(tag.name, tag.id, false, currentSelection.includes(String(tag.id)));
                tagsSelect.append(option);
            });

            tagsSelect.trigger('change');
        });
    }

    subjectSelect.on('change', function() {
        const subjectId = $(this).val();
        if (!subjectId) return;
        loadLessons(subjectId);
        loadTags(subjectId);
    });

    lessonSelect.on('change', function() {
        const lessonId = $(this).val();
        if (!lessonId) return;
        loadQuestionGroups(lessonId, false);
    });

    if (initial.lessonId) {
        loadQuestionGroups(initial.lessonId, true);
    }

    // Helper to get selected question type string (based on data-type attribute)
    function getSelectedType() {
        const selected = $('#questionType option:selected');
        const dataType = selected.data('type');
        if (dataType) return String(dataType);
        return '';
    }

    // Function to update hiddenChoices field
    function updateHiddenChoices() {
        const choices = [];
        $('.choiceEditor').each(function() {
            const choiceQuill = Quill.find(this);
            choices.push(choiceQuill.getContents()); // Store delta
        });
        $('#hiddenChoices').val(JSON.stringify(choices)); // Update hiddenChoices with delta
    }

    // Function to initialize choice editors
    function initializeChoiceEditors() {
        const choices = JSON.parse($('#hiddenChoices').val() || '[]');

        $('.choiceEditor').each(function(index) {
            if (!$(this).data('quill-initialized')) {
                const choiceQuill = new Quill(this, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['formula', 'link'],
                            [{ 'direction': 'rtl' }],
                            [{ 'align': [] }],
                            ['clean']
                        ]
                    },
                    placeholder: 'Enter choice text here...'
                });

                // load existing content
                if (choices[index]) {
                    choiceQuill.setContents(choices[index]);
                }

                choiceQuill.format('align','center');

                choiceQuill.on('text-change', function() {
                    $(this).closest('.border').find('.choiceInput').val(JSON.stringify(choiceQuill.getContents()));
                    updateHiddenChoices();
                });

                $(this).data('quill-initialized', true);
            }
        });
    }

    // Function to manage choices
    function manageChoices() {
        // حساب عدد الخيارات الحالية بدلاً من البدء من 2
        let choiceCount = $('#choicesContainer').children().length;

        $('#addChoice').click(function() {
            if (choiceCount < 10) {
                choiceCount++;
                const newChoice = `
                <div class="mb-2 border p-3 rounded">
                    <div class="choiceEditor question-editor ltr" style="height: 100px;" data-placeholder="Enter choice text here..."></div>
                    <input id="choice${choiceCount}" type="hidden" name="choice${choiceCount}" class="choiceInput" required>
                    <div class="mt-2 mx-4">
                        <input type="radio" name="correctAnswer" value="${choiceCount}" class="form-check-input" required>
                        <span class="mx-3">Correct Answer</span>
                    </div>
                </div>
            `;
                $('#choicesContainer').append(newChoice);
                initializeChoiceEditors();
                updateHiddenChoices();

                // إظهار زر الإزالة إذا كان هناك أكثر من خيارين
                if (choiceCount > 2) {
                    $('#removeChoice').show();
                }

                // إخفاء زر الإضافة إذا كان هناك 10 خيارات
                if (choiceCount === 10) {
                    $('#addChoice').hide();
                }
            }
        });

        $('#removeChoice').click(function() {
            if (choiceCount > 2) {
                $('#choicesContainer').children().last().remove();
                choiceCount--;
                updateHiddenChoices();

                // إخفاء زر الإزالة إذا كان هناك خياران فقط
                if (choiceCount === 2) {
                    $('#removeChoice').hide();
                }

                // إظهار زر الإضافة إذا كان هناك أقل من 10 خيارات
                if (choiceCount < 10) {
                    $('#addChoice').show();
                }
            }
        });

        // إخفاء زر الإزالة إذا كان هناك خياران فقط عند التحميل
        if (choiceCount <= 2) {
            $('#removeChoice').hide();
        }
        // إخفاء زر الإضافة إذا كان هناك 10 خيارات عند التحميل
        if (choiceCount === 10) {
            $('#addChoice').hide();
        }
    }

    // Function to load answer section based on question type
    function loadAnswerSection() {
        const selectedType = getSelectedType();
        let answerHtml = '';

        if (selectedType === 'TrueOrFalse') {
            answerHtml = `
                <label class="card-title mb-1">Answer</label>
                <select id="trueFalseSelector" class="form-control" name="correctAnswer" required>
                    <option value="" selected disabled>Select an answer</option>
                    <option value="1">True</option>
                    <option value="2">False</option>
                </select>
            `;
        } else if (selectedType === 'Automation') {
            // get existing
            const choices = JSON.parse($('#hiddenChoices').val() || '[]');
            const correctAnswer = $('#hiddenCorrectAnswer').val();

            let choicesHtml = '';
            choices.forEach((choice, index) => {
                choicesHtml += `
                <div class="mb-2 border p-3 rounded">
                    <div class="choiceEditor question-editor ltr" style="height: 100px;" data-placeholder="Enter choice text here..."></div>
                    <input id="choice${index+1}" type="hidden" name="choice${index+1}" class="choiceInput" required>
                    <div class="mt-2 mx-4">
                        <input type="radio" name="correctAnswer" value="${index+1}" class="form-check-input" required
                            ${correctAnswer == (index+1) ? 'checked' : ''}>
                        <span class="mx-3">Correct Answer</span>
                    </div>
                </div>
            `;
            });

            answerHtml = `
            <div class="d-flex align-items-center justify-content-between mb-3">
                <label class="card-title mb-1">Choices</label>
                <div>
                    <button type="button" class="btn btn-outline-success btn-sm" id="addChoice">Add Choice</button>
                    ${choices.length > 2 ? '<button type="button" class="btn btn-outline-danger btn-sm" id="removeChoice">Remove Choice</button>' : ''}
                </div>
            </div>
            <div id="choicesContainer">
                ${choicesHtml}
            </div>
        `;
        } else if (selectedType === 'NotAutomation') {
            answerHtml = `
                <label class="card-title mb-1">Correct Answer</label>
                <div class="question-editor ltr" id="correctAnswerEditor" style="height: 100px;" data-placeholder="Enter the correct answer here..."></div>
                <input type="hidden" name="correctAnswer" id="correctAnswer" required>
            `;
        }

        $('#answerSection').html(answerHtml);

        if (selectedType === 'Automation') {
            initializeChoiceEditors();
            manageChoices();

            // Load existing into editors
            const choices = JSON.parse($('#hiddenChoices').val() || '[]');
            choices.forEach((choice, index) => {
                const choiceQuill = Quill.find($('.choiceEditor').get(index));
                if (choiceQuill && choice) {
                    choiceQuill.setContents(choice);
                }
            });

            const correctAnswer = $('#hiddenCorrectAnswer').val();
            if (correctAnswer) {
                $(`input[name="correctAnswer"][value="${correctAnswer}"]`).prop('checked', true);
            }
        } else if (selectedType === 'TrueOrFalse') {
            const correctAnswer = $('#hiddenCorrectAnswer').val();
            if (correctAnswer) {
                $('#trueFalseSelector').val(correctAnswer);
            }
        } else if (selectedType === 'NotAutomation') {
            correctAnswerQuill = new Quill('#correctAnswerEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                        ['formula', 'link'],
                        [{ 'direction': 'rtl' }], // text direction
                        [{ 'align': [] }],
                        ['clean']
                    ]
                },
                placeholder: 'Enter the correct answer here...'
            });

            const choices = JSON.parse($('#hiddenChoices').val() || '[{}]');
            if (correctAnswerQuill && choices[0]) {
                correctAnswerQuill.setContents(choices[0]);
            }

            correctAnswerQuill.on('text-change', function() {
                $('#correctAnswer').val(JSON.stringify(correctAnswerQuill.getContents()));
            });
        }
    }

    // Load answer section based on current question type
    const currentQuestionType = $('#questionType').val();
    if (currentQuestionType) {
        loadAnswerSection();
    }

    // Handle question type change
    $('#questionType').change(function() {
        loadAnswerSection();
    });

    // Form Submission Validation
    $('form').on('submit', function(event) {
        let isValid = true;

        const questionText = quill.getContents();
        const hasContentText = questionText.ops.some(op => {
            return op.insert && (typeof op.insert !== 'string' || op.insert.trim() !== '');
        });

        if (!hasContentText) {
            alert('Please enter the question text.');
            isValid = false;
        }

        const selectedType = getSelectedType();

        // Check Choices (if multiple choice)
        if (selectedType === 'Automation') {
            $('.choiceEditor').each(function() {
                const choiceQuill = Quill.find(this);
                const choiceQuill1 = choiceQuill.getContents();
                const hasContentChoice = choiceQuill1.ops.some(op => {
                    return op.insert && (typeof op.insert !== 'string' || op.insert.trim() !== '');
                });

                if (!hasContentChoice) {
                    alert('Please fill in all choices.');
                    isValid = false;
                    return false; // Exit the loop
                }
            });

            // Check if a correct answer is selected
            if ($('input[name="correctAnswer"]:checked').length === 0) {
                alert('Please select the correct answer.');
                isValid = false;
            }
        } else if (selectedType === 'TrueOrFalse') {

            // Check trueFalse answer
            if ($('#trueFalseSelector').val() === null) {
                alert('Please select a correct choice.');
                isValid = false;
            }
        } else if (selectedType === 'NotAutomation') {

            const CorrectAnswerText = correctAnswerQuill.getContents();
            const hasContentAnswerText = CorrectAnswerText.ops.some(op => {
                return op.insert && (typeof op.insert !== 'string' || op.insert.trim() !== '');
            });

            if (!hasContentAnswerText) {
                alert('Please enter a correct answer.');
                isValid = false;
            }
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        } else {
            // Prepare data for submission
            if (selectedType === 'Automation') {
                updateHiddenChoices();
                const correctAnswer = $('input[name="correctAnswer"]:checked').val();
                $('#hiddenCorrectAnswer').val(correctAnswer);
            } else if (selectedType === 'TrueOrFalse') {
                const correctAnswer = $('#trueFalseSelector').val();
                $('#hiddenCorrectAnswer').val(correctAnswer);
            } else if (selectedType === 'NotAutomation') {
                // Store correct answer in choices for NotAutomation
                const correctAnswer = correctAnswerQuill.getContents();
                $('#hiddenChoices').val(JSON.stringify([correctAnswer]));
            }

            if (clearTagsInput.length && tagsSelect.length) {
                const hasSelection = (tagsSelect.val() || []).length > 0;
                if (hasSelection) {
                    clearTagsInput.val('0');
                }
            }

            // Continue with form submission
            return true;
        }
    });
});

// Image validation for question and hint photos
document.getElementById("questionPhoto").addEventListener("change", function(event) {
    const questionPhoto = event.target.files[0];

    if (questionPhoto) {
        const allowedTypes = ["image/jpeg", "image/png"];
        const maxSize = 1 * 1024 * 1024;

        if (!allowedTypes.includes(questionPhoto.type)) {
            alert('Images must be in jpg, png format');
            event.target.value = "";
        } else if (questionPhoto.size > maxSize) {
            alert('Image size must be less than 1MB');
            event.target.value = "";
        }
    }
});

document.getElementById("hintPhoto").addEventListener("change", function(event) {
    const hintPhoto = event.target.files[0];

    if (hintPhoto) {
        const allowedTypes = ["image/jpeg", "image/png"];
        const maxSize = 1 * 1024 * 1024;

        if (!allowedTypes.includes(hintPhoto.type)) {
            alert('Images must be in jpg, png format');
            event.target.value = "";
        } else if (hintPhoto.size > maxSize) {
            alert('Image size must be less than 1MB');
            event.target.value = "";
        }
    }
});
