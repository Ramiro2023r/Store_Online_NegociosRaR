<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\BenefitController as AdminBenefitController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\VariantController as AdminVariantController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ---------- Tienda pública ----------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/acerca-de', [HomeController::class, 'about'])->name('about');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// ---------- Búsqueda ----------
Route::get('/buscar/sugerencias', [SearchController::class, 'suggestions'])->name('search.suggestions');

// ---------- Páginas legales ----------
Route::get('/politica-de-privacidad', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terminos-y-condiciones', [LegalController::class, 'termsConditions'])->name('terms-conditions');
Route::get('/envio-y-devoluciones', [LegalController::class, 'shippingReturns'])->name('shipping-returns');

// ---------- Asistente RaR ----------
Route::middleware('auth')->prefix('asistente')->name('assistant.')->group(function () {
    Route::get('conversaciones', [\App\AssistantRAR\Controllers\AssistantController::class, 'conversations'])->name('conversations');
    Route::post('conversaciones', [\App\AssistantRAR\Controllers\AssistantController::class, 'createConversation'])->name('conversations.create');
    Route::get('conversaciones/{id}', [\App\AssistantRAR\Controllers\AssistantController::class, 'getConversation'])->name('conversations.get');
    Route::delete('conversaciones/{id}', [\App\AssistantRAR\Controllers\AssistantController::class, 'deleteConversation'])->name('conversations.delete');
    Route::post('conversaciones/{id}/mensaje', [\App\AssistantRAR\Controllers\AssistantController::class, 'sendMessage'])->name('send');
    Route::post('conversaciones/{id}/stream', [\App\AssistantRAR\Controllers\AssistantController::class, 'streamMessage'])->name('stream');
    Route::get('memorias', [\App\AssistantRAR\Controllers\AssistantController::class, 'memories'])->name('memories');
    Route::post('memorias', [\App\AssistantRAR\Controllers\AssistantController::class, 'saveMemory'])->name('memories.save');
    Route::delete('memorias/{key}', [\App\AssistantRAR\Controllers\AssistantController::class, 'deleteMemory'])->name('memories.delete');
});

// ---------- Carrito (invitados y clientes) ----------
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrito/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{item}', [CartController::class, 'remove'])->name('cart.remove');

// ---------- Autenticación ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store']);
    Route::get('/olvide-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/olvide-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ---------- Socialite (Google, Facebook) ----------
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

// ---------- Zona de clientes autenticados ----------
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/exito', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/mis-pedidos', [CheckoutController::class, 'myOrders'])->name('checkout.my-orders');
    Route::get('/mis-pedidos/{order}', [CheckoutController::class, 'show'])->name('checkout.show');

    Route::get('/contactanos/mensajes', [ContactController::class, 'messages'])->name('contact.messages');
    Route::post('/contactanos', [ContactController::class, 'send'])->name('contact.send');

    Route::post('/checkout/aplicar-cupon', [CouponController::class, 'apply'])->name('checkout.coupon.apply');
    Route::post('/checkout/quitar-cupon', [CouponController::class, 'remove'])->name('checkout.coupon.remove');
});

// ---------- Reseñas (clientes autenticados) ----------
Route::middleware('auth')->group(function () {
    Route::post('/productos/{product}/resenas', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/resenas/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// ---------- Direcciones (clientes autenticados) ----------
Route::middleware('auth')->group(function () {
    Route::get('/mis-direcciones', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/direcciones', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/direcciones/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/direcciones/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
});

// ---------- Lista de deseos (clientes autenticados) ----------
Route::middleware('auth')->group(function () {
    Route::get('/mi-lista-de-deseos', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/lista-de-deseos/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/lista-de-deseos/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// ---------- SEO ----------
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// ---------- Newsletter (público) ----------
Route::post('/newsletter/suscribir', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// ---------- Puntos fidelización ----------
Route::middleware('auth')->group(function () {
    Route::get('/mis-puntos', [LoyaltyController::class, 'index'])->name('loyalty.index');
});

// ---------- Comparar productos (público) ----------
Route::get('/comparar', [CompareController::class, 'index'])->name('compare.index');
Route::post('/comparar/{product}', [CompareController::class, 'toggle'])->name('compare.toggle');
Route::post('/comparar/limpiar', [CompareController::class, 'clear'])->name('compare.clear');

// Contacto: muestra chat si está logueado, o invita a login si no
Route::get('/contactanos', function () {
    if (auth()->check()) {
        return app(\App\Http\Controllers\ContactController::class)->index();
    }
    return view('contact-guest');
})->name('contact.index');

// ---------- Panel administrativo (admin + trabajador) ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,trabajador'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/history', [AdminInventoryController::class, 'history'])->name('inventory.history');
    Route::get('inventory/history/{product}', [AdminInventoryController::class, 'history'])->name('inventory.history.product');
    Route::get('inventory/restock/{product}/{variant?}', [AdminInventoryController::class, 'restockForm'])->name('inventory.restock');
    Route::post('inventory/restock/{product}/{variant?}', [AdminInventoryController::class, 'restockStore'])->name('inventory.restock.store');
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('products/{product}/variants', [AdminVariantController::class, 'index'])->name('products.variants.index');
    Route::post('products/{product}/variants', [AdminVariantController::class, 'store'])->name('products.variants.store');
    Route::get('products/{product}/variants/{variant}/edit', [AdminVariantController::class, 'edit'])->name('products.variants.edit');
    Route::put('products/{product}/variants/{variant}', [AdminVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [AdminVariantController::class, 'destroy'])->name('products.variants.destroy');
    Route::get('messages/{conversation}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::get('messages/{conversation}/messages', [AdminMessageController::class, 'messages'])->name('messages.messages');
    Route::post('messages/{conversation}/reply', [AdminMessageController::class, 'reply'])->name('messages.reply');

    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::resource('coupons', AdminCouponController::class);

    Route::resource('banners', AdminBannerController::class);
    Route::resource('benefits', AdminBenefitController::class)->except(['create', 'edit']);
    Route::resource('faqs', AdminFaqController::class)->except(['create', 'edit']);
    Route::get('newsletters', [AdminNewsletterController::class, 'index'])->name('newsletters.index');
    Route::delete('newsletters/{newsletter}', [AdminNewsletterController::class, 'destroy'])->name('newsletters.destroy');
    Route::get('newsletters-export', [AdminNewsletterController::class, 'export'])->name('newsletters.export');
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Solo admin gestiona usuarios/trabajadores
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
