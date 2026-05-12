document.getElementById('testBtn').addEventListener('click', async () => {
    const response = await fetch('/api/test');
    const data = await response.json();

    document.getElementById('output').textContent = JSON.stringify(data, null, 2);
});