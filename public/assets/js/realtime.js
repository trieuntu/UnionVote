// UnionVote Realtime Results - AJAX Polling

var pollingInterval = null;

function startPolling(electionId) {
    fetchResults(electionId);
    pollingInterval = setInterval(function() {
        fetchResults(electionId);
    }, 10000); // 10 seconds
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function fetchResults(electionId) {
    var baseUrl = document.querySelector('meta[name="base-url"]');
    var url = '/api/results/' + electionId;
    
    // Try to determine the base path from current URL
    var pathParts = window.location.pathname.split('/');
    var basePath = '';
    for (var i = 0; i < pathParts.length; i++) {
        if (pathParts[i] === 'results' || pathParts[i] === 'api') break;
        if (pathParts[i]) basePath += '/' + pathParts[i];
    }
    
    // Remove trailing parts after "public" 
    var publicIdx = basePath.indexOf('/public');
    if (publicIdx >= 0) {
        basePath = basePath.substring(0, publicIdx + '/public'.length);
    }
    
    url = basePath + '/api/results/' + electionId;

    fetch(url)
        .then(function(res) { return res.json(); })
        .then(function(data) { updateResultsUI(data); })
        .catch(function(err) { console.error('Polling error:', err); });
}

function updateResultsUI(data) {
    if (!data || !data.candidates) return;

    var totalVotedEl = document.getElementById('totalVoted');
    if (totalVotedEl) {
        totalVotedEl.textContent = data.total_voted;
    }

    var maxVotes = 1;
    data.candidates.forEach(function(c) {
        if (c.vote_count > maxVotes) maxVotes = c.vote_count;
    });

    data.candidates.forEach(function(c) {
        var item = document.querySelector('[data-candidate-id="' + c.id + '"]');
        if (!item) return;

        var infoEl = item.querySelector('.vote-info');
        if (infoEl) {
            infoEl.textContent = c.vote_count + ' phiếu (' + c.percentage + '%)';
        }

        var barEl = item.querySelector('.vote-bar');
        if (barEl) {
            var width = maxVotes > 0 ? Math.round((c.vote_count / maxVotes) * 100) : 0;
            barEl.style.width = width + '%';
        }
    });
}
