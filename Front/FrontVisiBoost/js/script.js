function startAnalysis() {
    let siteUrl = document.getElementById("siteUrl").value;
    if (siteUrl) {
        console.log("Analyse en cours pour :", siteUrl);
        alert("L'analyse de " + siteUrl + " est en cours !");
        // Ici, tu pourras faire un appel AJAX vers ton API PHP
    } else {
        alert("Veuillez entrer une URL valide.");
    }
}
function redirectToLogin() {
    window.location.href = "login.html";
}
