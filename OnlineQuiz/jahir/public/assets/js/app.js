document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var requiredInputs = form.querySelectorAll('[data-required="true"]');
            var valid = true;
            requiredInputs.forEach(function (input) {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('input-error');
                } else {
                    input.classList.remove('input-error');
                }
            });
            if (!valid) {
                e.preventDefault();
            }
        });
    });

    var timerContainer = document.getElementById('exam-timer');
    var examForm = document.getElementById('exam-form');
    if (timerContainer && examForm) {
        var remaining = parseInt(timerContainer.getAttribute('data-remaining') || '0', 10);
        var display = document.getElementById('timer-display');
        var submitted = false;

        function updateTimer() {
            if (remaining <= 0) {
                if (!submitted) {
                    submitted = true;
                    window.removeEventListener('beforeunload', beforeUnloadHandler);
                    examForm.submit();
                }
                return;
            }
            var minutes = Math.floor(remaining / 60);
            var seconds = remaining % 60;
            if (display) {
                display.textContent =
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }
            remaining -= 1;
        }

        function beforeUnloadHandler(e) {
            if (!submitted) {
                e.preventDefault();
                e.returnValue = '';
            }
        }

        updateTimer();
        setInterval(updateTimer, 1000);
        window.addEventListener('beforeunload', beforeUnloadHandler);
    }
});
