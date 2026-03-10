// UnionVote Frontend JS
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    document.querySelectorAll('.flash-message').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        }, 5000);
    });

    // Loading state for token request buttons
    document.querySelectorAll('.token-submit-btn').forEach(function(btn) {
        btn.closest('form').addEventListener('submit', function() {
            btn.disabled = true;
            var isLink = !btn.classList.contains('bg-blue-800');
            var spinnerColor = isLink ? 'text-blue-600' : 'text-white';
            btn.innerHTML = '<span class="inline-flex items-center justify-center gap-2">' +
                '<svg class="animate-spin h-4 w-4 ' + spinnerColor + '" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>' +
                '<span>Đang gửi mã...</span></span>';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    });
});
