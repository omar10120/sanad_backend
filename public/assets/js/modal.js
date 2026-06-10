    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modaldemo8').addClass(effect);
        });
        // hide modal with effect
        $('#modaldemo8').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });

    });

	$(document).ready(function() {
		$('.select2-show-search').select2({
		 minimumResultsForSearch: '',
		 placeholder: "Search",
		 width: '100%'
	   });
	});

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal1').addClass(effect);
        });
        // hide modal with effect
        $('#modal1').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });

    });

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal2').addClass(effect);
        });
        // hide modal with effect
        $('#modal2').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });

    });

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal3').addClass(effect);
        });
        // hide modal with effect
        $('#modal3').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });
    });

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal4').addClass(effect);
        });
        // hide modal with effect
        $('#modal4').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });
    });

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal5').addClass(effect);
        });
        // hide modal with effect
        $('#modal5').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });

    });

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal6').addClass(effect);
        });
        // hide modal with effect
        $('#modal6').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });
    });

    $(function() {
        'use strict'
        // showing modal with effect
        $('.modal-effect').on('click', function(e) {
            e.preventDefault();
            var effect = $(this).attr('data-effect');
            $('#modal7').addClass(effect);
        });
        // hide modal with effect
        $('#modal7').on('hidden.bs.modal', function(e) {
            $(this).removeClass(function(index, className) {
                return (className.match(/(^|\s)effect-\S+/g) || []).join(' ');
            });
        });
    });
