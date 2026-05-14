import './bootstrap';
import axios from 'axios';

const form = document.getElementById('form');
// serielize form data
form.addEventListener('submit', (e) => {
    e.preventDefault();
    const button = document.getElementById('submit-btn');
    button.disabled = true;
    button.textContent = 'Signing in...';
    const formData = new FormData(form);
    const data = {};
    for (const [key, value] of formData.entries()) {
        data[key] = value;
    }

    axios.post("/api/signin", data, {
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
        }
    })
    .then(response => {
        if(response.status === 200){
            console.log(response.data);
        }
    })
    .catch(err => {
        console.log(err);
        button.disabled = false;
        button.textContent = 'Sign In';
    })
    
});