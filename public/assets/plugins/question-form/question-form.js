$(document).ready(function() {
    // Initialize Quill for Question Text
    const quill = new Quill('#questionTextEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['formula', 'link'],

                [{ 'direction': 'rtl' }],                         // text direction

                [{ 'align': [] }],

                ['clean']
            ]
        },
        placeholder: 'Enter the question text here...'
    });

    quill.format('align','center');

    quill.on('text-change', function() {
        $('#questionText').val(JSON.stringify(quill.getContents())); // Store delta as JSON
    });

    // Initialize Quill for Hint
    const hintQuill = new Quill('#hintEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['formula', 'link'],

                [{ 'direction': 'rtl' }],                         // text direction

                [{ 'align': [] }],

                ['clean']
            ]
        },
        placeholder: 'Enter a hint to help the user...'
    });

    hintQuill.format('align','center');

    hintQuill.on('text-change', function() {
        $('#hint').val(JSON.stringify(hintQuill.getContents())); // Store delta as JSON
    });

    let correctAnswerQuill;

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
                // Update hiddenChoices before submission
                updateHiddenChoices();

                // Set correct answer
                const correctAnswer = $('input[name="correctAnswer"]:checked').val();
                $('#hiddenCorrectAnswer').val(correctAnswer);
            } else if (selectedType === 'TrueOrFalse') {
                // Set correct answer for true/false
                const correctAnswer = $('select[name="correctAnswer"]').val();
                $('#hiddenCorrectAnswer').val(correctAnswer);
            } else if (selectedType === 'NotAutomation') {
                // Set correct answer for other types
                $('#hiddenCorrectAnswer').val(JSON.stringify(correctAnswerQuill.getContents())); // Store delta
            }

            // Continue with form submission
            return true;
        }
    });

    // Manage Answers Based on Question Type
    $('#questionType').change(function() {
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
            answerHtml = `
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label class="card-title mb-1">Choices</label>
                    <div>
                        <button type="button" class="btn btn-outline-success btn-sm" id="addChoice">Add Choice</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="removeChoice">Remove Choice</button>
                    </div>
                </div>
                <div id="choicesContainer">
                    <div class="mb-2 border p-3 rounded">
                        <div class="choiceEditor question-editor ltr" style="height: 100px;" data-placeholder="Enter choice text here..."></div>
                        <input id="choice1" type="hidden" name="choice1" class="choiceInput" required>
                        <div class="mt-2 mx-4">
                            <input type="radio" name="correctAnswer" value="1" class="form-check-input" required><span class="mx-3">Correct Answer</span>
                        </div>
                    </div>
                    <div class="mb-2 border p-3 rounded">
                        <div class="choiceEditor question-editor ltr" style="height: 100px;" data-placeholder="Enter choice text here..."></div>
                        <input id="choice2" type="hidden" name="choice2" class="choiceInput" required>
                        <div class="mt-2 mx-4">
                            <input type="radio" name="correctAnswer" value="2" class="form-check-input" required><span class="mx-3">Correct Answer</span>
                        </div>
                    </div>
                    <div class="mb-2 border p-3 rounded">
                        <div class="choiceEditor question-editor ltr" style="height: 100px;" data-placeholder="Enter choice text here..."></div>
                        <input id="choice3" type="hidden" name="choice3" class="choiceInput" required>
                        <div class="mt-2 mx-4">
                            <input type="radio" name="correctAnswer" value="3" class="form-check-input" required><span class="mx-3">Correct Answer</span>
                        </div>
                    </div>
                    <div class="mb-2 border p-3 rounded">
                        <div class="choiceEditor question-editor ltr" style="height: 100px;" data-placeholder="Enter choice text here..."></div>
                        <input id="choice4" type="hidden" name="choice4" class="choiceInput" required>
                        <div class="mt-2 mx-4">
                            <input type="radio" name="correctAnswer" value="4" class="form-check-input" required><span class="mx-3">Correct Answer</span>
                        </div>
                    </div>
                </div>

            `;
        } else if (selectedType === 'NotAutomation') {
            answerHtml = `
                <label class="card-title mb-1">Correct Answer</label>
                <div class="question-editor ltr" id="correctAnswerEditor" style="height: 100px;" data-placeholder="Enter the correct answer here..."></div>
                <input type="hidden" name="correctAnswer" id="correctAnswer" required>
            `;
        } else {
            answerHtml = '';
        }

        $('#answerSection').html(answerHtml);

        if (selectedType === 'Automation') {
            initializeChoiceEditors();
            manageChoices();
        } else if (selectedType === 'NotAutomation') {
            correctAnswerQuill = new Quill('#correctAnswerEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['formula', 'link'],

                        [{ 'direction': 'rtl' }],                         // text direction

                        [{ 'align': [] }],

                        ['clean']
                    ]
                },
                placeholder: 'Enter the correct answer here...'
            });

            correctAnswerQuill.format('align','center');

            correctAnswerQuill.on('text-change', function() {
                $('#correctAnswer').val(correctAnswerQuill.getContents()); // Store delta
            });
        }
    });

    // تنفيذ دالة change عند تحميل الصفحة إذا كانت هناك قيمة محددة مسبقاً
        if ($('#questionType').val()) {
            $('#questionType').trigger('change');
        }    // Functions for Managing Choices
    function initializeChoiceEditors() {
        $('.choiceEditor').each(function() {
            if (!$(this).data('quill-initialized')) { // Check if Quill is already initialized
                const choiceQuill = new Quill(this, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                            ['formula', 'link'],

                            [{ 'direction': 'rtl' }],                         // text direction

                            [{ 'align': [] }],

                            ['clean']
                        ]
                    },
                    placeholder: 'Enter choice text here...'
                });

                choiceQuill.format('align','center');

                choiceQuill.on('text-change', function() {
                    $(this).closest('.border').find('.choiceInput').val(JSON.stringify(choiceQuill.getContents())); // Store delta
                    updateHiddenChoices(); // Update hiddenChoices when choice content changes
                });

                $(this).data('quill-initialized', true); // Mark as initialized
            }
        });
    }

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
});

document.getElementById("questionPhoto").addEventListener("change", function(event) {
    const questionPhoto = event.target.files[0];

    if (questionPhoto) {
        const allowedTypes = ["image/jpeg", "image/png"];
        const maxSize = 1 * 1024 * 1024;

        if (!allowedTypes.includes(questionPhoto.type)) {
            alert('Images must be in jpg,png format');
            event.target.value = "";
        } else if (questionPhoto.size > maxSize) {
            alert('Image size must be less 1MB');
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
            alert('Images must be in jpg,png format');
            event.target.value = "";
        } else if (hintPhoto.size > maxSize) {
            alert('Image size must be less 1MB');
            event.target.value = "";
        }
    }
});
