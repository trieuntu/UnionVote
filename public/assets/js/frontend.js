// UnionVote Frontend JS
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]').forEach(function(el) {
        if (el.closest('main')) {
            setTimeout(function() {
                el.style.transition = 'all 0.5s ease';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 500);
            }, 5000);
        }
    });
});
