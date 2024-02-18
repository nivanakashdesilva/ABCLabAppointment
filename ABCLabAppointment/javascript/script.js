function toggleLogin() {
    var loginContainer = document.getElementById("loginContainer");
    if (loginContainer.style.display === "block") {
        loginContainer.style.display = "none";
    } else {
        loginContainer.style.display = "block";
    }
}

function closeLogin() {
    var loginContainer = document.getElementById("loginContainer");
    loginContainer.style.display = "none";
}
