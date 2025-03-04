const urlBase = 'http://gerbtacts.com/LAMPAPI';

let userId = 0;
let firstname = "";
let lastname = "";

function doLogin() {
    let userId = 0;
    let firstname = "";
    let lastname = "";

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;
    
    if(!username || !password) {
        alert("Username and Password are required");
        return;
    }

    let data = {
        Username: username,
        Password: password
    };
    let payload = JSON.stringify(data);
    let url = urlBase + '/login.php';
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    try {
        xhr.onreadystatechange = function() {
            if (this.readyState == 4) {
                if(this.status == 200) {
                    let response = JSON.parse(xhr.responseText);
                    userId = response.id;
                    if(userId < 1) {		
                        alert("Username and password is incorrect")
                        return;
                    }

                    localStorage.setItem('jwt', response.token);
                    localStorage.setItem("username", username);
                    console.log("Token stored in cookie:", response.token); // Log the token
                
                    firstName = response.firstName;
                    lastName = response.lastName;
        
                    window.location.href = "contacts.html";
                }
                else {
                    alert("Cant Login! Status: " + xhr.status);
                }
            }
        };
        xhr.send(payload);
    }

    catch(error) {
        alert("Error has occurred");
    }
    
}   

function doRegister() {    
    let firstName = document.getElementById("firstName").value;
    let lastName = document.getElementById("lastName").value;
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    let data = {
        firstName: firstName,
        lastName: lastName,
        username: username,
        password: password
    };
    
    let payload = JSON.stringify(data);
    let url = urlBase + '/register.php';
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    /*
    if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(xhr.responseText);
    }
    */
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) { // Request is complete
            if (xhr.status === 200) { // Successful response
                let response = JSON.parse(xhr.responseText);
                if (response.success === "Registered successfully") {
                    alert("Account registered successfully!"); // Show success alert
                } else {
                    alert("Error: " + response.message); // Show error message
                }
            } else {
                alert("Cant Register Contact! Status: " + xhr.status); // Show HTTP error
            }
        }
    };
    
    try {
        xhr.send(payload);
    }
    catch(error) {
        alert("Error has occurred" + error.message);
    }

}
