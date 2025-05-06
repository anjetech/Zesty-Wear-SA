document.getElementById("searchForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Stop form from reloading

    let query = document.getElementById("searchInput").value.toLowerCase().trim(); // Correct property & remove extra spaces

    let pages = {
        "home": "main.html",
        "men": "men.html",
        "woman": "woman.html",
        "accessories": "accessories.html",
        "men jeans": "men-image1-description.html",
        "men's jeans": "men-image1-description.html",
        "winter jacket": "description2-men.html",
        "winter jackets": "description2-men.html",
        "jackets": "description2-men.html",
        "men's black t-shirt": "description3-men.html",
        "mens black t-shirt": "description3-men.html",
        "black t-shirt": "description3-men.html",
        "men's white t-shirt": "description4-men.html",
        "mens white t-shirt": "description4-men.html",
        "white t-shirt": "description4-men.html",
        "woman jeans": "woman-description1.html",
        "jeans": "woman-description1.html",
        "bootleg jeans": "woman-description1.html",
        "jeans suit": "woman-description2.html",
        "woman jeans suit": "woman-description2.html",
        "black leather jacket": "woman-description3.html",
        "woman black leather jacket": "woman-description3.html",
        "leather jacket": "woman-description3.html",
        "white dress": "woman-description4.html",
        "dress": "woman-description4.html",
        "wallet": "accessories1-men.html",
        "watch": "accessories2-men.html",
        "belt": "accessories3-men.html",
        "sneakers": "accessories4-men.html",
        "sneaker": "accessories4-men.html",
        "earrings": "accessories1-woman.html",
        "handbag": "accessories2-woman.html",
        "make up bag": "accessories3-woman.html",
        "necklace": "accessories4-woman.html"
    };

    if (pages[query]) {
        window.location.href = pages[query]; // Redirect user
    } else {
        alert("Page not found! Please check your spelling or try a different keyword."); // Better error message
    }
});
