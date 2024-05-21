document.getElementById("showPassword").innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
document.getElementById("showPassword").addEventListener("click", function() {
    var passwordInput = document.getElementById("password");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        document.getElementById("showPassword").innerHTML = '<i class="fa-solid fa-eye"></i>';
    } else {
        passwordInput.type = "password";
        document.getElementById("showPassword").innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
    }
});