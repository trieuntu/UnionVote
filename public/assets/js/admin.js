// UnionVote Admin JS
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const sidebar = document.querySelector('aside');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('z-50');
                sidebar.classList.toggle('inset-y-0');
                sidebar.classList.toggle('left-0');
            }
        });
    }

    // Auto-hide flash messages after 5 seconds
    document.querySelectorAll('.flash-message').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        }, 5000);
    });
});
