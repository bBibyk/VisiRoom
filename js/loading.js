/*function appliquerStyleChargement() {
    const body = document.body;
    body.style.margin = "0";
    body.style.height = "100vh";
    body.style.backgroundColor = "#ffffff";
    body.style.display = "flex";
    body.style.justifyContent = "center";
    body.style.alignItems = "center";
    body.style.fontFamily = "sans-serif";

    // Supprimer les styles inutiles pour le chargement
    body.style.padding = "";
    body.style.backgroundImage = "";
    body.style.background = "";
    body.style.textAlign = "";
    body.style.color = "";
}

function cacherMessage() {
    appliquerStyleChargement();
    document.getElementById("page").style.display = "none";
    document.getElementById("container-loading").style.display = "block";
    document.getElementById("loader").style.display = "block";
    document.getElementById("message").style.display = "block";
}

// Exécute la fonction quand le bouton est cliqué
document.addEventListener("DOMContentLoaded", function () {
    const bouton = document.getElementById("htmlButton");
    bouton.addEventListener("click", cacherMessage);
});

document.addEventListener("DOMContentLoaded", function () {
    const bouton = document.getElementById("searchButton");
    bouton.addEventListener("click", cacherMessage);
});