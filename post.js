document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".post-form");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const id = document.getElementById("post-id").value.trim();
        const title = document.getElementById("post-title").value.trim();
        const desc = document.getElementById("post-description").value.trim();
        const image = document.getElementById("post-image").files[0];

        // Contrôles de saisie
        if (!id || !title || !desc || !image) {
            alert("Tous les champs sont obligatoires !");
            return;
        }

        const formData = new FormData();
        formData.append("id", id);
        formData.append("titre", title);
        formData.append("description", desc);
        formData.append("image", image);

        try {
            const response = await fetch("postC.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert("Ajout avec succès");
                afficherAnnonce(result.post);
                form.reset();
            } else {
                alert("Erreur : " + result.message);
            }
        } catch (err) {
            alert("Erreur de connexion au serveur");
            console.error(err);
        }
    });

    function afficherAnnonce(post) {
        const container = document.getElementById("posts-container");

        const card = document.createElement("div");
        card.className = "post-card";
        card.innerHTML = `
            <h3>${post.titre}</h3>
            <p>${post.description}</p>
            <img src="uploads/${post.image}" alt="image" style="max-width: 300px;">
            <div class="post-actions">
                <button onclick="modifierAnnonce('${post.id}')" class="btn btn-outline">Modifier</button>
                <button onclick="supprimerAnnonce('${post.id}', this)" class="btn btn-primary">Supprimer</button>
            </div>
        `;
        container.appendChild(card);
    }
 
    

  
});
