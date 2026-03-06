// UnionVote Voting JS - Ballot checkbox logic

function initBallot(minVotes, maxVotes) {
    const checkboxes = document.querySelectorAll('.candidate-checkbox');
    const countEl = document.getElementById('selectedCount');
    const reviewBtn = document.getElementById('reviewBtn');

    function updateState() {
        const checked = document.querySelectorAll('.candidate-checkbox:checked').length;
        
        // Update counter
        if (countEl) {
            countEl.textContent = 'Đã chọn: ' + checked + '/' + maxVotes;
        }

        // Enable/disable review button
        if (reviewBtn) {
            reviewBtn.disabled = checked < minVotes || checked > maxVotes;
        }

        // Disable unchecked boxes if max reached
        checkboxes.forEach(function(cb) {
            if (!cb.checked && checked >= maxVotes) {
                cb.disabled = true;
                cb.closest('.candidate-item').classList.add('opacity-50');
            } else {
                cb.disabled = false;
                cb.closest('.candidate-item').classList.remove('opacity-50');
            }

            // Highlight selected
            if (cb.checked) {
                cb.closest('.candidate-item').classList.add('selected', 'border-blue-400', 'bg-blue-50');
            } else {
                cb.closest('.candidate-item').classList.remove('selected', 'border-blue-400', 'bg-blue-50');
            }
        });
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateState);
    });

    updateState();
}
