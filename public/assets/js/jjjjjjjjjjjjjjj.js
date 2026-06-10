(function($) {
    "use strict";
    $('#questionType').change(function() {
        $('#multipleChoiceSection').toggle($(this).val() === 'multipleChoice');
        $('#answerSection').toggle($(this).val() !== 'multipleChoice');
    });

// Add/remove choices
    $('#addChoice').click(function() {
        let choiceCount = $('#choicesContainer .choice').length;
        let newChoice = `<div class="choice">
                    <input type="text" class="form-control choice-text" placeholder="Choice ${choiceCount + 1}">
                    <input type="radio" name="correctAnswer" value="${choiceCount}"> Correct?
                    </div>`;
        $('#choicesContainer').append(newChoice);
    });

    $('#removeChoice').click(function() {
        if ($('#choicesContainer .choice').length > 1) {
            $('#choicesContainer .choice:last-child').remove();
        }
    });

// Photo Preview
    $('#photoUpload').change(function() {
        let file = this.files[0];
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#photoPreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    });

// ... (similar code for photoHintPreview) ...

// Tag handling (you'll likely use a library or custom implementation for this)
// ...

// Form Submission (handle data submission to server)
    $('#questionForm').submit(function(e) {
        e.preventDefault();
        // ... (Collect data and send it to your server) ...
    });

})();

