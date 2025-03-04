const urlBase = 'http://gerbtacts.com/LAMPAPI';
currentMode = 'register'



function displayUsername() {
    let user = localStorage.getItem("username")
    const userElement = document.getElementById("username-tag");
    userElement.textContent = user;
}


function openMenu(mode = 'register', contact = null) {
    currentMode = mode;
    if(mode == 'edit' && contact) {
        document.getElementById('cFirstName').value = contact.FirstName || '';
        document.getElementById('cLastName').value = contact.LastName || '';
        document.getElementById('email').value = contact.Email || '';
        document.getElementById('phoneNumber').value = contact.Phone || '';
        document.getElementById('contactId').value = contact.ID || '';
        document.getElementById('register-button').textContent = 'Confirm Changes';
    }
    else {
        document.getElementById('cFirstName').value = '';
        document.getElementById('cLastName').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phoneNumber').value = '';
        document.getElementById('contactId').value = '';
        document.getElementById('register-button').textContent = 'Register'; 
    }
    document.getElementById('menu').style.display = 'block';
}

function closeMenu() {
    document.getElementById('menu').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    fetchContacts();
});

function editContact(contactID) {
    const token = localStorage.getItem('jwt');

    if (!token) {
        alert("Error: No JWT token found. Please log in again.");
        return;
    }

    let firstName = document.getElementById('cFirstName').value;
    let lastName = document.getElementById('cLastName').value;
    let email = document.getElementById('email').value;
    let phone = document.getElementById('phoneNumber').value;

    let data = {
        ID: contactID,
        FirstName: firstName,
        LastName: lastName,
        Email: email,
        Phone: phone
    }
    
    console.log(data);

    let payload = JSON.stringify(data);
    let url = urlBase + '/edit_contact.php';
    let xhr = new XMLHttpRequest();
    
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Authorization", "Bearer " + token);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) { // Request is complete
            if (xhr.status === 200) { // Successful response
                let response = JSON.parse(xhr.responseText);
                console.log("Status Code:", xhr.status); // Log the status code
                console.log("Response:", xhr.responseText); // Log the response text
                if (response.success === "Contact updated successfully") {
                    alert("Contact edited successfully!"); // Show success alert
                } 
                else {
                    alert("Error: " + response.message); // Show error message
                }
            } else {
                alert("Error: Request failed with status " + xhr.status); // Show HTTP error
            }
        }
    }

    try {
        xhr.send(payload);
        location.reload();
    }
    catch(error) {
        alert("Error has occurred" + error.message);
    }
}

function handleMode() {
    if (currentMode === 'register') {
        registerContact();
    } else if (currentMode === 'edit') {
        let contactID = document.getElementById('contactId').value;
        editContact(contactID);
    }
}

function deleteContact(contactID) {
    const token = localStorage.getItem('jwt');

    if (!token) {
        alert("Error: No JWT token found. Please log in again.");
        return;
    }



    let data = {
        ID: contactID,
    };

    console.log("Sending payload:", data);
    let payload = JSON.stringify(data);
    let url = urlBase + '/delete_contact.php';
    let xhr = new XMLHttpRequest();

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("Authorization", "Bearer " + token);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) { 
                let response = JSON.parse(xhr.responseText);
                if (response.success === "Contact deleted successfully") {
                    alert("Contact removed successfully!");
                } 
                else {
                    alert("Error: " + response.message);
                }
            } else {
                alert("Error: Request failed with status " + xhr.status);
            }
        }
    }

    xhr.send(payload);
    location.reload();

    xhr.onerror = function () {
        // Handle network errors
        console.error("Network error: Failed to send request");
    };
}

// Function to get a cookie by name
function getCookie(name) {
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
    return null;
}
// J added this delete if not useful
function fetchContacts() {
    const token = localStorage.getItem('jwt');

    if (!token) {
        console.error('No token found');
        return;
    }



    console.log('Fetching contacts...');
    fetch(`${urlBase}/retrieve_contacts.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            pageNum: 1,
            contactsPerPage: 10
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('API Response:', data);
        totalContacts = data.totalContacts;

        const contactsBody = document.getElementById('contacts-body');
        contactsBody.innerHTML = '';

        data.contacts.forEach(contact => {
            const row = document.createElement('tr');
            row.classList.add('contacts-row');

            // Your SVG icons
            const deleteIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 14" fill="none"><path d="M14.9907 6.10205L16.1501 4.94266C16.3103 4.78248 16.3103 4.52315 16.1501 4.36297L15.5704 3.78327C15.4102 3.62309 15.1509 3.62309 14.9907 3.78327L13.8313 4.94266L12.6719 3.78327C12.5117 3.62309 12.2524 3.62309 12.0922 3.78327L11.5125 4.36297C11.3524 4.52315 11.3524 4.78248 11.5125 4.94266L12.6719 6.10205L11.5125 7.26144C11.3524 7.42162 11.3524 7.68096 11.5125 7.84114L12.0922 8.42083C12.2524 8.58101 12.5117 8.58101 12.6719 8.42083L13.8313 7.26144L14.9907 8.42083C15.1509 8.58101 15.4102 8.58101 15.5704 8.42083L16.1501 7.84114C16.3103 7.68096 16.3103 7.42162 16.1501 7.26144L14.9907 6.10205ZM5.69525 6.50885C7.49281 6.50885 8.94967 5.05199 8.94967 3.25443C8.94967 1.45686 7.49281 0 5.69525 0C3.89769 0 2.44082 1.45686 2.44082 3.25443C2.44082 5.05199 3.89769 6.50885 5.69525 6.50885ZM7.97335 7.32246H7.54875C6.98431 7.5818 6.3563 7.72927 5.69525 7.72927C5.03419 7.72927 4.40873 7.5818 3.84175 7.32246H3.41715C1.5306 7.32246 0 8.85306 0 10.7396V11.7973C0 12.4711 0.546642 13.0177 1.22041 13.0177H10.1701C10.8439 13.0177 11.3905 12.4711 11.3905 11.7973V10.7396C11.3905 8.85306 9.8599 7.32246 7.97335 7.32246Z" fill="#6E6D6D"/></svg>`;
            const editIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M7.38215 2.36717L10.6326 5.61759L3.57442 12.6757L0.67641 12.9957C0.288452 13.0386 -0.0393326 12.7105 0.00383034 12.3226L0.326283 9.42253L7.38215 2.36717ZM12.643 1.88324L11.1168 0.357046C10.6407 -0.119015 9.86859 -0.119015 9.39253 0.357046L7.95673 1.79285L11.2072 5.04327L12.643 3.60747C13.119 3.13115 13.119 2.3593 12.643 1.88324Z" fill="#6E6D6D"/></svg>`;

            row.innerHTML = `
                <td class="contacts-item">${contact.FirstName}</td>
                <td class="contacts-item">${contact.LastName}</td>
                <td class="contacts-item">${contact.Email}</td>
                <td class="contacts-item">${contact.Phone}</td>
                <td class="contacts-button-container">
                    <button class="contacts-button edit-button" data-id="${contact.ID || ''}">${editIconSvg}</button>
                    <button class="contacts-button delete-button" data-id="${contact.ID || ''}">${deleteIconSvg}</button>
                </td>
            `;

            // Add event listeners
            row.querySelector('.edit-button').addEventListener('click', (e) => {
                const contactId = e.currentTarget.getAttribute('data-id');
                openMenu('edit', contact);
            });

            row.querySelector('.delete-button').addEventListener('click', (e) => {
                const contactId = e.currentTarget.getAttribute('data-id');
                console.log(`Delete contact ${contactId}`);
                deleteContact(contactId);
            });

            contactsBody.appendChild(row);
        });
    })
    .catch(error => console.error('Error fetching contacts:', error));
}
// End of what J added


function searchContacts() {
    const token = localStorage.getItem('jwt');
    const searchQuery = document.getElementById('SearchBar').value.trim(); // Get search input

    if (!token) {
        console.error('No token found');
        return;
    }

    if (searchQuery === '') {
        // If search query is empty, fetch all contacts
        fetchContacts();
        return;
    }

    console.log('searching contacts...');
    fetch(`${urlBase}/search_contacts.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            search: searchQuery, // Add search term
            pageNum: 1,
            contactsPerPage: 10
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('API Response:', data);

        const contactsBody = document.getElementById('contacts-body');
        contactsBody.innerHTML = '';

        data.contacts.forEach(contact => {
            const row = document.createElement('tr');
            row.classList.add('contacts-row');

            // Your SVG icons
            const deleteIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 14" fill="none"><path d="M14.9907 6.10205L16.1501 4.94266C16.3103 4.78248 16.3103 4.52315 16.1501 4.36297L15.5704 3.78327C15.4102 3.62309 15.1509 3.62309 14.9907 3.78327L13.8313 4.94266L12.6719 3.78327C12.5117 3.62309 12.2524 3.62309 12.0922 3.78327L11.5125 4.36297C11.3524 4.52315 11.3524 4.78248 11.5125 4.94266L12.6719 6.10205L11.5125 7.26144C11.3524 7.42162 11.3524 7.68096 11.5125 7.84114L12.0922 8.42083C12.2524 8.58101 12.5117 8.58101 12.6719 8.42083L13.8313 7.26144L14.9907 8.42083C15.1509 8.58101 15.4102 8.58101 15.5704 8.42083L16.1501 7.84114C16.3103 7.68096 16.3103 7.42162 16.1501 7.26144L14.9907 6.10205ZM5.69525 6.50885C7.49281 6.50885 8.94967 5.05199 8.94967 3.25443C8.94967 1.45686 7.49281 0 5.69525 0C3.89769 0 2.44082 1.45686 2.44082 3.25443C2.44082 5.05199 3.89769 6.50885 5.69525 6.50885ZM7.97335 7.32246H7.54875C6.98431 7.5818 6.3563 7.72927 5.69525 7.72927C5.03419 7.72927 4.40873 7.5818 3.84175 7.32246H3.41715C1.5306 7.32246 0 8.85306 0 10.7396V11.7973C0 12.4711 0.546642 13.0177 1.22041 13.0177H10.1701C10.8439 13.0177 11.3905 12.4711 11.3905 11.7973V10.7396C11.3905 8.85306 9.8599 7.32246 7.97335 7.32246Z" fill="#6E6D6D"/></svg>`;
            const editIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M7.38215 2.36717L10.6326 5.61759L3.57442 12.6757L0.67641 12.9957C0.288452 13.0386 -0.0393326 12.7105 0.00383034 12.3226L0.326283 9.42253L7.38215 2.36717ZM12.643 1.88324L11.1168 0.357046C10.6407 -0.119015 9.86859 -0.119015 9.39253 0.357046L7.95673 1.79285L11.2072 5.04327L12.643 3.60747C13.119 3.13115 13.119 2.3593 12.643 1.88324Z" fill="#6E6D6D"/></svg>`;

            row.innerHTML = `
                <td class="contacts-item">${contact.FirstName}</td>
                <td class="contacts-item">${contact.LastName}</td>
                <td class="contacts-item">${contact.Email}</td>
                <td class="contacts-item">${contact.Phone}</td>
                <td class="contacts-button-container">
                    <button class="contacts-button edit-button" data-id="${contact.ID || ''}">${editIconSvg}</button>
                    <button class="contacts-button delete-button" data-id="${contact.ID || ''}">${deleteIconSvg}</button>
                </td>
            `;

            // Add event listeners
            row.querySelector('.edit-button').addEventListener('click', (e) => {
                const contactId = e.currentTarget.getAttribute('data-id');
                console.log(`Edit contact ${contactId}`);
                openMenu('edit', contact);
            });

            row.querySelector('.delete-button').addEventListener('click', (e) => {
                const contactId = e.currentTarget.getAttribute('data-id');
                console.log(`Delete contact ${contactId}`);
                deleteContact(contactID);
            });

            contactsBody.appendChild(row);
        });
    })
    .catch(error => console.error('Error fetching contacts:', error));
}

// ** Add Enter key event listener to the search bar **
document.getElementById('SearchBar').addEventListener('input', function () {
    searchContacts(); // Call your search function whenever input changes
});

function registerContact() {
    let firstName = document.getElementById("cFirstName").value;
    let lastName = document.getElementById("cLastName").value;
    let email = document.getElementById("email").value;
    let phone = document.getElementById("phoneNumber").value;

    let data = {
        firstName: firstName,
        lastName: lastName,
        phone: phone,
        email: email
    };
    const token = localStorage.getItem('jwt');
    if (!token) {
        alert("Error: No JWT token found. Please log in again.");
        return;
    }
    let payload = JSON.stringify(data);
    let url = urlBase + '/add_contact.php';
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Authorization", "Bearer " + token);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) { // Request is complete
            if (xhr.status === 201) { // Successful response
                let response = JSON.parse(xhr.responseText);
                console.log("Status Code:", xhr.status); // Log the status code
                console.log("Response:", xhr.responseText); // Log the response text
                if (response.success === "Contact added successfully") {
                    alert("Contact registered successfully!"); // Show success alert
                } 
                else {
                    alert("Error: " + response.message); // Show error message
                }
            } else {
                alert("Error: Request failed with status " + xhr.status); // Show HTTP error
            }
        }
    }

    try {
        xhr.send(payload);
        location.reload();
    }
    catch(error) {
        alert("Error has occurred" + error.message);
    }
}

displayUsername();



document.getElementById("signOutButton").addEventListener("click", function () {
    console.log("Signing out...");
    localStorage.removeItem("jwt");
    localStorage.removeItem("username"); // Optional but recommended
    window.location.href = "/index.html"; // Redirects to domain root + index.html

});
