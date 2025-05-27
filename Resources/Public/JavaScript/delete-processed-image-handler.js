// Resources/Public/JavaScript/delete-processed-image-handler.js

class DeleteProcessedImageHandler {
    constructor() {
        document.addEventListener('click', this.handleClick.bind(this));
    }

    handleClick(event) {
        const button = event.target.closest('[data-action="delete-processed-image"]');
        if (!button) {
            return;
        }

        event.preventDefault();
        const url = button.getAttribute('data-url');
        if (!url) {
            console.error('Missing URL for delete-processed-image action.');
            return;
        }

        if (!confirm('Are you sure you want to delete all processed files for this image?')) {
            return;
        }

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    top.TYPO3.Notification.success('Success', data.message);
                } else {
                    top.TYPO3.Notification.error('Error', data.message);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                top.TYPO3.Notification.error('Error', 'AJAX request failed.');
            });
    }
}

export default new DeleteProcessedImageHandler();