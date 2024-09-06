import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';

export default class RadiologiesWishlistPlugin extends Plugin {
    static options = {
        createURL: '/radiologies/wishlist/create',
        addURL: '/radiologies/wishlist/add'
    };
    init() {
        this._client = new HttpClient();
        this._registerEvents();
    }

    _registerEvents() {
        const wishlistButton = document.getElementById('wishlistButton');
        wishlistButton.addEventListener('click', this._onWishlistButtonClick.bind(this));

        const createWishlistButton = document.getElementById('createWishlistButton');
        createWishlistButton.addEventListener('click', this._onCreateWishlistClick.bind(this));

        const addToWishlistButton = document.getElementById('addToWishlistButton');
        addToWishlistButton.addEventListener('click', this._onAddToWishlistClick.bind(this));
        document.getElementById('sortType').addEventListener('change', function() {
            const searchQueryInput = document.getElementById('searchQuery');
            if (this.value === 'manual') {
                searchQueryInput.style.display = 'block';
            } else {
                searchQueryInput.style.display = 'none';
            }
        });
        
    }

    _onWishlistButtonClick() {
        document.getElementById('wishlistModal').classList.add('show');
        document.getElementById('wishlistModal').style.display = 'block';

    }

    _onCreateWishlistClick() {
        const wishlistName = document.getElementById('wishlistName').value;
        const productId = this._getProductId();

        if (!wishlistName) {
            alert('Please enter a wishlist name');
            return;
        }

        this._client.post(this.options.createURL, JSON.stringify({ wishlistName: wishlistName, productId: productId }), (response) => {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Wishlist created and product added!');
                location.reload(); // Reload to update the wishlist dropdown
            } else {
                alert('Failed to create wishlist');
            }
        });
    }

    _onAddToWishlistClick() {
        const selectedWishlistId = document.getElementById('existingWishlists').value;
        const productId = this._getExistingProductId();

        if (!selectedWishlistId) {
            alert('Please select a wishlist');
            return;
        }

        this._client.post(this.options.addURL, JSON.stringify({ wishlistId: selectedWishlistId, productId: productId }), (response) => {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Product added to wishlist!');
                $('#wishlistModal').modal('hide');
            } else {
                alert('Failed to add product to wishlist');
            }
        });
    }

    _getProductId() {
        return document.querySelector('input[name="wishlistProductId"]').value;
    }
    _getExistingProductId() {
        return document.querySelector('input[name="radiologiesWishlistProductId"]').value;
    }
    
}
