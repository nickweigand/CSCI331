const serverBaseUrl = "http://localhost/FinalProject";

let currentUser = null;

document.addEventListener("DOMContentLoaded", () => {
    setupEventListeners();
    updateUI();
});

// Setup Event Listeners
function setupEventListeners() {
    document.getElementById("auth-form").addEventListener("submit", (event) => {
        event.preventDefault();
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        login(email, password);
    });

    document.getElementById("logout-btn")?.addEventListener("click", logout);

    document.getElementById("search-btn").addEventListener("click", () => {
        const query = document.getElementById("search-bar").value;
        if (query) {
            searchRecipes(query);
        } else {
            alert("Please enter a search term.");
        }
    });
}

// Login Function
async function login(email, password) {
    try {
        const response = await fetch(`${serverBaseUrl}/login.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password }),
        });

        const data = await response.json();
        if (data.success) {
            currentUser = data.user;
            alert(data.message);
            updateUI();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error("Login failed:", error);
        alert("An error occurred during login. Please try again.");
    }
}

// Logout Function
async function logout() {
    try {
        const response = await fetch(`${serverBaseUrl}/logout.php`, {
            method: "POST",
        });

        const data = await response.json();
        if (data.success) {
            currentUser = null;
            alert("Logged out successfully.");
            updateUI();
        }
    } catch (error) {
        console.error("Logout failed:", error);
        alert("An error occurred during logout. Please try again.");
    }
}

// Search Recipes
async function searchRecipes(query) {
    try {
        const response = await fetch(`${serverBaseUrl}/test_api.php?query=${encodeURIComponent(query)}`, {
            method: "GET",
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const recipesText = await response.text();
        displayGeneratedRecipes(recipesText);
    } catch (error) {
        console.error("Search failed:", error);
        alert("Failed to fetch recipes. Please try again.");
    }
}

function displayGeneratedRecipes(recipesText) {
    const container = document.getElementById("recipe-cards");
    const recipes = recipesText.split('-----').map(recipe => recipe.trim());

    container.innerHTML = recipes.map(recipe => {
        const lines = recipe.split('\n\n\n').filter(line => line.trim() !== '');

        const title = lines.find(line => line.startsWith("Title:"))?.replace("Title:", "").trim() || "No Title";
        const url = lines.find(line => line.startsWith("URL:"))?.replace("URL:", "").trim() || "Not available";
        const imgUrl = lines.find(line => line.startsWith("Image URL:"))?.replace("Image URL:", "").trim() || "https://via.placeholder.com/150";

        const ingredientsIndex = lines.findIndex(line => line.startsWith("Ingredients:"));
        const directionsIndex = lines.findIndex(line => line.startsWith("Directions:"));

        const ingredients = ingredientsIndex >= 0
            ? lines.slice(ingredientsIndex + 1, directionsIndex).map(ing => `<li>${ing}</li>`).join('')
            : "<li>No ingredients available</li>";

        const directions = directionsIndex >= 0
            ? lines.slice(directionsIndex + 1).map(step => `<li>${step}</li>`).join('')
            : "<li>No directions available</li>";

        return `
            <div class="recipe-card">
                <h2>${title}</h2>
                ${url !== "Not available" ? `<a href="${url}" target="_blank">${url}</a>` : "<p>No URL available</p>"}
                <img src="${imgUrl}" alt="Recipe Image" style="width:100%; max-height:200px; object-fit:cover;">
                <h3>Ingredients:</h3>
                <ul>${ingredients}</ul>
                <h3>Directions:</h3>
                <ol>${directions}</ol>
            </div>
        `;
    }).join('');
}








// Update UI
function updateUI() {
    const authSection = document.getElementById("auth-section");
    const searchSection = document.getElementById("search-section");

    if (currentUser) {
        authSection.style.display = "none";
        searchSection.style.display = "block";
    } else {
        authSection.style.display = "block";
        searchSection.style.display = "none";
    }
}




