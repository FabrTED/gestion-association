function setupSearch(inputId) {
    const input = document.getElementById(inputId);

    if (!input) return;

    input.addEventListener("keyup", function () {
        const value = this.value.toLowerCase();

        const table = input.closest("body").querySelector("table");
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? "" : "none";
        });
    });
}

// 🔹 activer
setupSearch("searchActivite");
setupSearch("searchMembre");
setupSearch("searchUser");


// ✔ Validation simple pour TOUS les formulaires
document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", function (e) {
        const inputs = form.querySelectorAll("input[required]");
        for (let input of inputs) {
            if (input.value.trim() === "") {
                alert("Veuillez remplir les champs obligatoires");
                e.preventDefault();
                return;
            }
        }
    });
});

