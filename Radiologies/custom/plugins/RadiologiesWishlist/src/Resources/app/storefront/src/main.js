// Import all necessary Storefront plugins
import RadiologiesWishlistPlugin from './radiologies-wishlist-plugin/radiologies-wishlist-plugin.plugin';

// Register your plugin via the existing PluginManager
const PluginManager = window.PluginManager;

PluginManager.register('RadiologiesWishlistPlugin', RadiologiesWishlistPlugin, '[data-add-to-radiologies-wishlist]');
