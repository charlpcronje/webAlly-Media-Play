// File: public/assets/js/media_tracking.js

document.addEventListener('DOMContentLoaded', function() {
    // Track video interactions
    const videos = document.querySelectorAll('video');
    videos.forEach(video => {
        video.addEventListener('play', () => {
            logMediaAction(video, 'play');
        });
        video.addEventListener('pause', () => {
            logMediaAction(video, 'pause');
        });
        video.addEventListener('timeupdate', () => {
            const progress = Math.floor((video.currentTime / video.duration) * 100);
            logMediaAction(video, 'progress', progress);
        });
    });

    // Track audio interactions
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        audio.addEventListener('play', () => {
            logMediaAction(audio, 'play');
        });
        audio.addEventListener('pause', () => {
            logMediaAction(audio, 'pause');
        });
        audio.addEventListener('timeupdate', () => {
            const progress = Math.floor((audio.currentTime / audio.duration) * 100);
            logMediaAction(audio, 'progress', progress);
        });
    });

    // Function to log media actions
    function logMediaAction(mediaElement, action, progress = 0) {
        const mediaId = mediaElement.dataset.mediaId;
        const pageId = mediaElement.dataset.pageId;

        if (!mediaId || !pageId) {
            console.warn('Media ID or Page ID not set for tracking.');
            return;
        }

        fetch('/admin/media/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                media_id: mediaId,
                page_id: pageId,
                action: action,
                current_time: progress
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to log media action:', data.message);
            }
        })
        .catch(error => {
            console.error('Error logging media action:', error);
        });
    }
});
