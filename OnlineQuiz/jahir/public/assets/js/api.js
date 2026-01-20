/**
 * API Helper for Online Exam System
 */

const API = {
    // 1. GET Operation
    async getExamDetails(examId) {
        try {
            const response = await fetch(`${BASE_URL}api/exam/details?id=${examId}`);
            const data = await response.json();
            
            if (data.success) {
                alert(`Details:\nTitle: ${data.data.title}\nDesc: ${data.data.description}`);
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('API Error:', error);
        }
    },

    // 2. POST Operation
    async createExam(examData) {
        try {
            const response = await fetch(`${BASE_URL}api/exam/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(examData)
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Exam Created! ID: ' + data.id);
                location.reload(); // Reload to show new exam
            } else {
                alert('Failed: ' + data.message);
            }
        } catch (error) {
            console.error('API Error:', error);
        }
    },

    // 3. DELETE Operation
    async deleteExam(examId, rowElement) {
        if (!confirm('Are you sure you want to delete this exam via AJAX?')) return;

        try {
            const response = await fetch(`${BASE_URL}api/exam/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: examId })
            });
            const data = await response.json();

            if (data.success) {
                // Remove the row from the table smoothly
                if (rowElement) {
                    rowElement.style.background = '#ffcccc';
                    setTimeout(() => rowElement.remove(), 500);
                }
                console.log('Deleted successfully');
            } else {
                alert('Delete failed: ' + data.message);
            }
        } catch (error) {
            console.error('API Error:', error);
        }
    }
};

// Expose API to window so we can call it from HTML
window.API = API;
