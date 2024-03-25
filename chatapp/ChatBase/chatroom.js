document.addEventListener('DOMContentLoaded', function () {
    const sendMessageButton = document.getElementById('send-message');
    const messageInput = document.getElementById('message-input');
    const messageDisplay = document.getElementById('message-display');
    const chatroomId = new URLSearchParams(window.location.search).get('chatroomid');

    sendMessageButton.addEventListener('click', function () {
        const message = messageInput.value.trim();
        if (message && chatroomId) {
            fetch('send_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `message=${encodeURIComponent(message)}&chatroomid=${encodeURIComponent(chatroomId)}`
            })
            .then(response => response.text())
            .then(text => {
                if (text.trim() === 'success') {
                    messageInput.value = '';
                    // No need to manually call refreshMessages here since it will be called automatically
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    function refreshMessages() {
        if (chatroomId) {
            fetch(`get_messages.php?chatroomid=${encodeURIComponent(chatroomId)}`)
                .then(response => response.text())
                .then(text => messageDisplay.innerHTML = text)
                .catch(error => console.error('Error:', error));
        }
    }

    // Call refreshMessages() if chatroomId is present
    if (chatroomId) {
        refreshMessages();
        // Automatically refresh messages every 5 seconds
        setInterval(refreshMessages, 5000);
    }
});
