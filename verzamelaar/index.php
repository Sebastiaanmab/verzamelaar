<?php
// Maak verbinding met de database (vervang deze gegevens door je eigen databasegegevens)
$servername = "localhost";
$username = "JanDeMan";
$password = "Settlover11";
$dbname = "db_89606";
// Maak de databaseverbinding
$conn = new mysqli($servername, $username, $password, $dbname);
// Controleer de verbinding
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Query om 8 items uit de database op te halen (vervang 'verzamelaar' door de juiste tabelnaam)
$sql = "SELECT * FROM verzamelaar LIMIT 8";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie-edge">
    <title>verzamelaar_89606</title>
    <link rel="stylesheet" href="src/css/index.css">
    <style>
        /* Shared modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            background-color: black;
            margin: 10% auto;
            padding: 20px;
            width: 60%;
            color: white;
            box-shadow: 0px 0px 4px 1px rgb(255 255 255);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .shopping-cart-modal {
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            background-color: black;
            color: white;
            height: 100%;
            border-left: 1px solid #888;
            overflow-y: scroll;
            display: none;
            z-index: 2000;
            padding-top: 80px;
            box-shadow: 0px 0px 1px 0px rgb(255 255 255);
        }

        .shopping-cart-modal-content {

        }

        #shopping-cart-list ul {
            list-style-type: none;
            padding: 0;
        }
        #shopping-cart-list ul li {
            padding: 10px;
            border-bottom: 1px solid #888;
        }
    </style>
</head>
<body>
<div class="header">
    <nav>
        <ul>
            <!-- Add smooth scrolling to the Home link -->
            <li><a href="#">Home</a></li>
            <li><a href="#box-container">Services</a></li>
            <!-- Add a link for the shopping cart in the main menu -->
            <li><a href="#shopping-cart" id="shopping-cart-link">Checkout</a></li>
        </ul>
    </nav>
</div>
<div class="main">
    <div class="main-head">
        <video autoplay loop muted>
            <source src="src/img/main4.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <h1>Verzamelaar</h1>
        <p>Bij Verzamelaar zijn we gepassioneerd over film. Wij bieden legale filmkopieën aan, zodat je kunt genieten van jouw favoriete films. Onze gevarieerde collectie omvat klassiekers en recente hits. Ontdek jouw volgende filmavontuur en steun de filmindustrie met Verzamelaar.</p>
    </div>
    <!-- Shopping cart content modal -->
    <div id="shopping-cart-list" class="shopping-cart-modal">
        <div id="shopping-cart-content-modal">
            <span class="close" id="shopping-cart-close-button">&times;</span>
            <h2>Winkelwagen</h2>
            <!-- Input fields for customer information -->

            <ul id="cart-items-list"></ul>
            <div id="customer-info-form" style="display: none; margin-bottom: 10px;">
                <input type="text" id="first-name" placeholder="Voornaam">
                <input type="text" id="last-name" placeholder="Achternaam">
                <input type="email" id="email" placeholder="E-mail">
                <input type="text" id="address" placeholder="Adres">
            </div>
            <button id="checkout-button" class="checkout-button">Checkout</button>
        </div>
    </div>
    <div class="box-container" id="box-container">
        <?php
        // Loop through the results and create a div box for each item
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $itemId = $row["id"];
                $itemName = $row["naam"];
                $itemMakers = $row["makers"];
                $itemGenre = $row["genre"];
                $itemUitkomst = $row["uitkomst"];
                $itemRating = $row["rating"];
                $itemImagePath = $row["image_path"];
                // Create a div box for each item with data attributes
                echo '<div class="box" data-id="' . $itemId . '" data-name="' . $itemName . '" data-makers="' . $itemMakers . '" data-genre="' . $itemGenre . '" data-uitkomst="' . $itemUitkomst . '" data-rating="' . $itemRating . '">';
                echo '<div class="box-left" style="background-image: url(' . $itemImagePath . '); min-height: 300px;"></div>';
                echo '<div class="box-right">';
                echo '<h2>' . $itemName . '</h2>';
                echo '<p data-info="makers">Makers: ' . $itemMakers . '</p>';
                echo '<p data-info="genre">Genre: ' . $itemGenre . '</p>';
                echo '<p data-info="uitkomst">Uitkomst: ' . $itemUitkomst . '</p>';
                echo '<p data-info="rating">Rating: ' . $itemRating . '</p>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>
<!-- Modal for item details -->
<div id="contentModal" class="modal">
    <div class="modal-content" id="content-modal">
        <span class="close" id="content-modal-close-button">&times;</span>
        <h2 id="content-modal-title"></h2>
        <p id="content-modal-makers"></p>
        <p id="content-modal-genre"></p>
        <p id="content-modal-uitkomst"></p>
        <p id="content-modal-rating"></p>
        <button class="buy-button" id="content-modal-buy-button">Toevoegen</button>
    </div>
</div>
<script>
    // Get the modal and close button elements for the content modal
    var contentModal = document.getElementById("contentModal");
    var contentModalCloseButton = document.getElementById("content-modal-close-button");

    // Shopping cart data
    var shoppingCart = [];

    // Function to add an item to the shopping cart
    function addToCart(itemId, itemName) {
        var existingItem = shoppingCart.find(item => item.id === itemId);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            shoppingCart.push({ id: itemId, name: itemName, quantity: 1 });
        }

        // Show the checkout button when an item is added
        var checkoutButton = document.getElementById("checkout-button");
        checkoutButton.style.display = "block";
    }

    // Function to update the shopping cart list
    function updateCartList() {
        var cartItemsList = document.getElementById("cart-items-list");
        cartItemsList.innerHTML = '';
        shoppingCart.forEach(function (item) {
            var listItem = document.createElement("li");
            listItem.textContent = item.name + (item.quantity > 1 ? ' (x' + item.quantity + ')' : '');
            cartItemsList.appendChild(listItem);
        });
    }

    // Handle the "Add to Cart" button click event within the content modal
    var contentModalBuyButton = document.getElementById("content-modal-buy-button");
    contentModalBuyButton.addEventListener("click", function () {
        var itemId = contentModalBuyButton.getAttribute("data-id");
        var itemName = document.getElementById("content-modal-title").textContent;
        // Add the item to the shopping cart
        addToCart(itemId, itemName);
        // Update the shopping cart list
        updateCartList();
        // Close the content modal
        contentModal.style.display = "none";
    });

    // Handle the item box click event to open the content modal
    var itemBoxes = document.querySelectorAll(".box");
    itemBoxes.forEach(function (box) {
        box.addEventListener("click", function () {
            var itemId = this.getAttribute("data-id");
            var itemName = this.querySelector("h2").textContent;
            var itemMakers = this.querySelector("p[data-info='makers']").textContent;
            var itemGenre = this.querySelector("p[data-info='genre']").textContent;
            var itemUitkomst = this.querySelector("p[data-info='uitkomst']").textContent;
            var itemRating = this.querySelector("p[data-info='rating']").textContent;
            // Set the content modal content for the selected item
            var contentModalTitle = document.getElementById("content-modal-title");
            var contentModalMakers = document.getElementById("content-modal-makers");
            var contentModalGenre = document.getElementById("content-modal-genre");
            var contentModalUitkomst = document.getElementById("content-modal-uitkomst");
            var contentModalRating = document.getElementById("content-modal-rating");
            contentModalTitle.textContent = itemName;
            contentModalMakers.textContent = "Makers: " + itemMakers;
            contentModalGenre.textContent = "Genre: " + itemGenre;
            contentModalUitkomst.textContent = "Uitkomst: " + itemUitkomst;
            contentModalRating.textContent = "Rating: " + itemRating;
            // Set the "Add to Cart" button's data-id attribute
            contentModalBuyButton.setAttribute("data-id", itemId);
            // Show the content modal
            contentModal.style.display = "block";
        });
    });

    // Close the content modal when the close button is clicked
    contentModalCloseButton.addEventListener("click", function () {
        contentModal.style.display = "none";
    });

    // Get the modal and close button elements for the shopping cart modal
    var shoppingCartList = document.getElementById("shopping-cart-list");
    var shoppingCartContentModal = document.getElementById("shopping-cart-content-modal");
    var shoppingCartCloseButton = document.getElementById("shopping-cart-close-button");

    // Hide the checkout button initially
    var checkoutButton = document.getElementById("checkout-button");
    checkoutButton.style.display = "none";

    // Message for an empty cart
    var emptyCartMessage = document.createElement("p");
    emptyCartMessage.textContent = "Uw winkelwagen is leeg. Voeg items toe aan uw winkelwagen.";
    emptyCartMessage.style.display = "none";
    shoppingCartContentModal.appendChild(emptyCartMessage);

    // Function to check if the shopping cart is empty
    function isCartEmpty() {
        return shoppingCart.length === 0;
    }

    // Function to update the shopping cart modal content
    function updateShoppingCartModal() {
        var cartItemsList = document.getElementById("cart-items-list");
        var customerInfoForm = document.getElementById("customer-info-form"); // Add this line

        if (isCartEmpty()) {
            // Display the empty cart message
            emptyCartMessage.style.display = "block";
            cartItemsList.style.display = "none";
            checkoutButton.style.display = "none"; // Hide the checkout button
            customerInfoForm.style.display = "none"; // Hide the customer info form
        } else {
            // Hide the empty cart message
            emptyCartMessage.style.display = "none";
            cartItemsList.style.display = "block";
            customerInfoForm.style.display = "block"; // Show the customer info form
            cartItemsList.innerHTML = '';
            shoppingCart.forEach(function (item) {
                var listItem = document.createElement("li");
                listItem.textContent = item.name + (item.quantity > 1 ? ' (x' + item.quantity + ')' : '');
                cartItemsList.appendChild(listItem);
            });
            checkoutButton.style.display = "block"; // Show the checkout button
        }
    }

    // Handle the "Shopping Cart" link click event to open the shopping cart modal
    var shoppingCartLink = document.getElementById("shopping-cart-link");
    shoppingCartLink.addEventListener("click", function () {
        // Update the shopping cart modal content
        updateShoppingCartModal();
        // Show the shopping cart list
        shoppingCartList.style.display = "block";
    });

    // Handle the "Checkout" button click event
    var checkoutButton = document.getElementById("checkout-button");
    checkoutButton.addEventListener("click", function () {
        var firstName = document.getElementById("first-name").value;
        var lastName = document.getElementById("last-name").value;
        var email = document.getElementById("email").value;
        var address = document.getElementById("address").value;

        if (isCartEmpty()) {
            alert("Uw winkelwagen is leeg. Voeg items toe aan uw winkelwagen voordat u afrekent.");
        } else if (!firstName || !lastName || !email || !address) {
            alert("Vul alstublieft uw volledige persoonlijke informatie in voordat u afrekent.");
        } else {
            // Simulate a checkout process (you can replace this with your actual checkout logic)
            alert("Afrekenen succesvol. Bedankt voor uw aankoop!");

            // Clear the shopping cart
            shoppingCart = [];
            // Update the shopping cart modal content
            updateShoppingCartModal();
            checkoutButton.style.display = "none"; // Hide the checkout button

            // Clear the form fields
            document.getElementById("first-name").value = "";
            document.getElementById("last-name").value = "";
            document.getElementById("email").value = "";
            document.getElementById("address").value = "";

            // Close the shopping cart modal
            shoppingCartList.style.display = "none";
        }
    });

    // Close the shopping cart modal when the close button is clicked
    shoppingCartCloseButton.addEventListener("click", function () {
        shoppingCartList.style.display = "none";
    });

</script>
</body>
</html>

<?php
// Sluit de databaseverbinding
$conn->close();
?>
