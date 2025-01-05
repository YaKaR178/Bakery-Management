// cart.js

// Initialize or retrieve the basket from localStorage to persist data across page reloads
let basket = JSON.parse(localStorage.getItem('basket')) || {};

function updateQuantityUI(sku, quantity) {
    // Update quantity in catalog view
    const qtyElementCatalog = document.getElementById(`qty-${sku}`);
    if (qtyElementCatalog) {
        qtyElementCatalog.textContent = quantity; // Update quantity in catalog view
    }

    // Update quantity in cart view
    const qtyElementCart = document.getElementById(sku);
    if (qtyElementCart) {
        qtyElementCart.textContent = quantity; // Update quantity in cart view

        // Update the total price in the cart view
        const totalElement = document.getElementById(`total-${sku}`);
        if (totalElement) {
            const price = parseFloat(totalElement.dataset.price); // Price of the product
            totalElement.textContent = `${(quantity * price).toFixed(2)} NIS`;
        }
    }

    updateCartAmount(); // Update the total quantity in the cart icon
    saveBasket(); // Save the basket in localStorage
}


// Function to save the basket to localStorage
function saveBasket() {
    // Save the basket to localStorage
    localStorage.setItem('basket', JSON.stringify(basket));
    updateCartAmount(); // Update the cart icon amount
}


// Function to increment the quantity of an item
function increment(sku) {
    if (!basket[sku]) {
        basket[sku] = 0;
    }
    basket[sku]++;
    updateQuantityUI(sku, basket[sku]); // Update the quantity in the UI
    saveBasket(); // Save the basket
    renderCartPage(); // Re-render the cart
}



// Function to decrement the quantity of an item
function decrement(sku) {
    if (basket[sku]) {
        basket[sku]--; // Decrease quantity of the product
        if (basket[sku] <= 0) {
            delete basket[sku]; // Remove the product from the basket if quantity is 0

            // Update the quantity to 0 in catalog view
            const qtyElementCatalog = document.getElementById(`qty-${sku}`);
            if (qtyElementCatalog) {
                qtyElementCatalog.textContent = 0; // Update the quantity to 0
            }

            // Remove the item from the cart view
            const itemElement = document.getElementById(`cart-item-${sku}`);
            if (itemElement) {
                itemElement.remove();
            }
        } else {
            // Update the quantity in UI if the quantity is still greater than 0
            updateQuantityUI(sku, basket[sku]);
        }
    }

    // If the basket is empty after the action, show a message in the cart page
    if (Object.keys(basket).length === 0) {
        const shoppingCartContainer = document.getElementById('shopping-cart');
        if (shoppingCartContainer) {
            shoppingCartContainer.innerHTML = '<p>Your cart is empty</p>';
        }
    }

    updateCartAmount(); // Update the cart icon amount
    saveBasket(); // Save the basket in localStorage
    renderCartPage(); // Update the cart page
}


// Function to remove an item from the cart
function removeItem(sku) {
    if (basket[sku]) {
        delete basket[sku];
        saveBasket();
        document.getElementById(`cart-item-${sku}`).remove();
        updateCartAmount();
        renderCartPage(); // Update the cart page
    }
}

// Function to update the cart amount in the header
function updateCartAmount() {
    const cartAmountElement = document.getElementById('cart-amount');
    if (cartAmountElement) {
        const totalQuantity = Object.values(basket).reduce((sum, qty) => sum + qty, 0);
        cartAmountElement.textContent = totalQuantity;
    }
}

// Function to render the shopping cart on cart.php
async function renderCartPage() {
    const shoppingCartContainer = document.getElementById('shopping-cart');
    if (!shoppingCartContainer) return;

    shoppingCartContainer.innerHTML = '';

    if (Object.keys(basket).length === 0) {
        shoppingCartContainer.innerHTML = '<p>Your cart is empty</p>';
        return;
    }

    let totalPrice = 0; // Calculate the total price
    let description = ''; // Initialize the description string

    // Fetch product data from the database
    try {
        const response = await fetch('getItems.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const { success, items } = await response.json();
        if (!success) {
            throw new Error('API response indicates failure.');
        }

        Object.keys(basket).forEach(sku => {
            const search = items.find(product => product.SKU === sku);
            if (!search) return;



            const qty = basket[sku];

            description += `SKU: ${sku}, Name: ${search.item_name}, Quantity: ${qty}\n`;

            const itemDiv = document.createElement('div');
            itemDiv.classList.add('cart-item');
            itemDiv.id = `cart-item-${sku}`;
            itemDiv.innerHTML = `
                <div class="details">
                    <img src="img/feature/${search.image}" alt="${search.item_name}"></img>
                    <div class="title-price-x">
                        <h4 class="title-price">
                            <h4>${search.item_name}</h4>
                            <p class="cart-item-price">&#8362 ${search.price} </p>
                        </h4>

                        <div class="buttons">
                            <i onclick="decrement('${sku}')" class='fas fa-minus'></i>
                            <div id=${sku} class="quantity">${qty}</div>
                            <i onclick="increment('${sku}')" class='fas fa-plus'></i>
                        </div>

                        <h3 id="total-${sku}" data-price="${search.price}">&#8362 ${(qty * search.price).toFixed(2)}</h3>

                        <i onclick="removeItem('${sku}')" class="fa fa-close"></i>
                    </div>
                </div>
            `;
            shoppingCartContainer.appendChild(itemDiv);

            // Update the total price
            totalPrice += qty * search.price;
        });

        // Check for a discounted price
        const discountedPrice = parseFloat(localStorage.getItem('totalPriceWithDiscount'));
        localStorage.setItem('description', description.trim());
        // If a discount exists, use it as the base total price
        const finalPrice = !isNaN(discountedPrice) ? totalPrice - discountedPrice : totalPrice;

        localStorage.setItem('finalPrice', finalPrice); //NEED TO CHECK THIS LINE BECAUSE ITS MAKE PROBLEMS

        // Display the total price
        const totalPriceElement = document.createElement('div');
        totalPriceElement.innerHTML = `
            <h2 class="cart-total">Total: &#8362;${finalPrice.toFixed(2)}</h2>
            <button class="checkout" onclick="window.location.href = 'checkout.php';">Checkout</button>
            <button class="remove-all" onclick="clearCart()">Clear Cart</button>
        `;
        shoppingCartContainer.appendChild(totalPriceElement);



    } catch (error) {
        console.error('Failed to fetch product data:', error);
        shoppingCartContainer.innerHTML = '<p>Failed to load cart items. Please try again later.</p>';
    }
}


// Event listener for DOMContentLoaded - loads data from localStorage and updates UI
window.addEventListener('DOMContentLoaded', () => {
    // Load the basket from localStorage
    basket = JSON.parse(localStorage.getItem('basket')) || {};

    // Update quantities in the catalog page
    Object.keys(basket).forEach(sku => {
        const qtyElementCatalog = document.getElementById(`qty-${sku}`);
        if (qtyElementCatalog) {
            qtyElementCatalog.textContent = basket[sku]; // Display the saved quantity
        }
    });

    // Update quantities in the cart page
    renderCartPage();

    // Update the cart icon
    updateCartAmount();
});


//CLEAR THE WHOLE CART FUNC
function clearCart() {
    basket = {}; // Clear the basket
    localStorage.setItem('basket', JSON.stringify(basket)); // Save the empty basket

    // Remove the discounted price
    localStorage.removeItem('totalPriceWithDiscount');
    localStorage.removeItem('finalPrice');


    // Update the UI
    const shoppingCartContainer = document.getElementById('shopping-cart');
    if (shoppingCartContainer) {
        shoppingCartContainer.innerHTML = '<p>Your cart is empty</p>';
    }

    updateCartAmount(); // Update the cart icon amount
}




//COUPON HANDLER
document.addEventListener('DOMContentLoaded', () => {
    const applyCouponButton = document.querySelector('.promo-code button[name="promoCode"]');

    // Check if the coupon was already redeemed in the session


    if (applyCouponButton) {
        applyCouponButton.addEventListener('click', async () => {
            const couponCode = document.getElementById('promo-code-input').value.trim();
            const label = document.getElementById('label');

            if (!couponCode) {
                label.textContent = 'Please enter a coupon code.';
                label.style.color = 'red';
                return;
            }

            try {
                // Fetch the total price from the rendered cart
                let totalPrice = 0;
                const totalPriceElement = document.querySelector('.cart-total');
                if (totalPriceElement) {
                    totalPrice = parseFloat(totalPriceElement.textContent.replace(/[^0-9.]/g, ''));
                }

                if (isNaN(totalPrice) || totalPrice <= 0) {
                    label.textContent = 'Invalid total price. Ensure your cart is not empty.';
                    label.style.color = 'red';
                    return;
                }

                // Send the coupon code and total price to the server
                const response = await fetch('applyCoupon.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ coupon_code: couponCode, total_price: totalPrice }),
                });

                const data = await response.json();

                if (data.success) {
                    label.textContent = `Coupon applied! You saved ${data.discount_amount.toFixed(2)} \u20AA`;
                    label.style.color = 'green';

                    // Update the displayed total price
                    if (totalPriceElement) {
                        totalPriceElement.textContent = `Total: \u20AA${data.final_price.toFixed(2)}`;
                    }

                    // Save the discounted price for the checkout page
                    localStorage.setItem('totalPriceWithDiscount', data.final_price);
                    localStorage.setItem('finalPrice', data.final_price);

                    // Set flag in sessionStorage to mark the coupon as redeemed
                    sessionStorage.setItem('couponRedeemed', 'true'); // Store coupon redeemed status in session

                    // Disable the coupon button after redemption
                    applyCouponButton.disabled = true;

                } else {
                    label.textContent = data.message;
                    label.style.color = 'red';
                }
            } catch (error) {
                console.error('Error applying coupon:', error);
                label.textContent = 'An error occurred. Please try again.';
                label.style.color = 'red';
            }
        });
    }
});



