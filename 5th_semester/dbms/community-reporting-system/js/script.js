document.addEventListener('DOMContentLoaded', function() {
    const alertMessages = document.querySelectorAll('.alert');
    alertMessages.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });

    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = this.previousElementSibling;
                if (label) {
                    label.textContent = label.textContent.split(' (')[0] + ' (' + fileName + ')';
                }
            }
        });
    }

    const confirmDeletes = document.querySelectorAll('[onclick*="confirm"]');
    confirmDeletes.forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (!confirm(this.getAttribute('onclick').match(/'([^']+)'/)?.[1] || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });
});
