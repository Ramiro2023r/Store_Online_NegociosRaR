<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ---------- Tienda pública ----------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/acerca-de', [HomeController::class, 'about'])->name('about');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// ---------- Páginas legales ----------
Route::get('/politica-de-privacidad', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terminos-y-condiciones', [LegalController::class, 'termsConditions'])->name('terms-conditions');

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

// ---------- Zona de clientes autenticados ----------
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/exito', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/mis-pedidos', [CheckoutController::class, 'myOrders'])->name('checkout.my-orders');

    Route::post('/contactanos', [ContactController::class, 'send'])->name('contact.send');

    Route::post('/checkout/aplicar-cupon', [CouponController::class, 'apply'])->name('checkout.coupon.apply');
    Route::post('/checkout/quitar-cupon', [CouponController::class, 'remove'])->name('checkout.coupon.remove');
});

// ---------- Reseñas (clientes autenticados) ----------
Route::middleware('auth')->group(function () {
    Route::post('/productos/{product}/resenas', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/resenas/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// ---------- Lista de deseos (clientes autenticados) ----------
Route::middleware('auth')->group(function () {
    Route::get('/mi-lista-de-deseos', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/lista-de-deseos/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/lista-de-deseos/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

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

    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{conversation}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{conversation}/reply', [AdminMessageController::class, 'reply'])->name('messages.reply');

    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::resource('coupons', AdminCouponController::class);

    // Solo admin gestiona usuarios/trabajadores
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
