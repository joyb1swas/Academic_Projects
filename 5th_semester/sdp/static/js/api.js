const api = {
    async get(endpoint) {
        try {
            const response = await fetch(endpoint);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error("GET Error:", error);
            alert("Failed to fetch data.");
            return [];
        }
    },
    
    async post(endpoint, data) {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                const errorData = await response.json();
                alert(errorData.error || "Failed to process request");
                return null;
            }
            return await response.json();
        } catch (error) {
            console.error("POST Error:", error);
            alert("Failed to submit data.");
            return null;
        }
    },
    
    async delete(endpoint) {
        try {
            const response = await fetch(endpoint, {
                method: 'DELETE'
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                alert(errorData.error || "Failed to process request");
                return false;
            }
            return true;
        } catch (error) {
            console.error("DELETE Error:", error);
            alert("Failed to delete data.");
            return false;
        }
    }
};
