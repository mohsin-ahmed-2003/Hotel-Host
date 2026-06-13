<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserProfileController;

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\SocialLoginController;

use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\SpaceTypeController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\HostPropertyController;

Route::resource('users', UserController::class);
Route::get('/', [HomeController::class, 'index'])->name('homepage');

Route::middleware('web')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user/properties', [\App\Http\Controllers\UserPropertyController::class, 'index'])->name('user.properties');
    Route::get('/trips', [\App\Http\Controllers\UserReservationController::class, 'trips'])->name('user.trips');
    Route::get('/reservations', [\App\Http\Controllers\UserReservationController::class, 'reservations'])->name('user.reservations');
    Route::get('/reservations/{reservation}/itinerary', [\App\Http\Controllers\UserReservationController::class, 'itinerary'])->name('user.reservations.itinerary');
    Route::get('/reservations/{reservation}/receipt', [\App\Http\Controllers\UserReservationController::class, 'receipt'])->name('user.reservations.receipt');
});
Route::get('/rooms', [\App\Http\Controllers\RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [\App\Http\Controllers\RoomController::class, 'show'])->name('rooms.show');
Route::post('/rooms/{room}/calculate-price', [\App\Http\Controllers\RoomController::class, 'calculatePrice'])->name('rooms.calculate-price');
Route::match(['GET', 'POST'], '/rooms/{room}/booking', [\App\Http\Controllers\RoomController::class, 'booking_page'])->name('rooms.booking_page');

// Payment Routes
Route::post('/payment/paypal/initiate', [\App\Http\Controllers\PaymentController::class, 'initiatePaypal'])->name('payment.paypal.initiate');
Route::get('/payment/paypal/success', [\App\Http\Controllers\PaymentController::class, 'paypalSuccess'])->name('payment.paypal.success');
Route::get('/payment/paypal/cancel', [\App\Http\Controllers\PaymentController::class, 'paypalCancel'])->name('payment.paypal.cancel');

Route::post('/payment/stripe/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateStripe'])->name('payment.stripe.initiate');
Route::get('/payment/stripe/success', [\App\Http\Controllers\PaymentController::class, 'stripeSuccess'])->name('payment.stripe.success');
Route::get('/payment/stripe/cancel', [\App\Http\Controllers\PaymentController::class, 'stripeCancel'])->name('payment.stripe.cancel');

Route::post('/payment/easebuzz/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateEasebuzz'])->name('payment.easebuzz.initiate');
Route::post('/payment/easebuzz/success', [\App\Http\Controllers\PaymentController::class, 'easebuzzSuccess'])->name('payment.easebuzz.success');
Route::post('/payment/easebuzz/cancel', [\App\Http\Controllers\PaymentController::class, 'easebuzzCancel'])->name('payment.easebuzz.cancel');

Route::post('/payment/razorpay/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateRazorpay'])->name('payment.razorpay.initiate');
Route::post('/payment/razorpay/success', [\App\Http\Controllers\PaymentController::class, 'razorpaySuccess'])->name('payment.razorpay.success');
Route::post('/payment/razorpay/cancel', [\App\Http\Controllers\PaymentController::class, 'razorpayCancel'])->name('payment.razorpay.cancel');

// Phone verification
Route::get('/verify-phone/{token}', [PhoneVerificationController::class, 'show'])->name('phone.verify');
Route::post('/verify-phone/{token}/send', [PhoneVerificationController::class, 'sendOtp'])->name('phone.send.otp');
Route::post('/verify-phone/{token}/verify', [PhoneVerificationController::class, 'verifyOtp'])->name('phone.verify.otp');

// Profile phone verification
Route::post('/profile/phone/send-otp', [PhoneVerificationController::class, 'sendProfileOtp'])->name('profile.phone.send');
Route::post('/profile/phone/verify-otp', [PhoneVerificationController::class, 'verifyProfileOtp'])->name('profile.phone.verify');

// Email verification
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verify'])->name('email.verify');
Route::post('/email/send-verification', [EmailVerificationController::class, 'send'])->name('email.send.verification');

// User profile
Route::get('/profile', [UserProfileController::class, 'show'])->name('user.profile');
Route::post('/profile/update', [UserProfileController::class, 'update'])->name('user.profile.update');
Route::post('/profile/password', [UserProfileController::class, 'updatePassword'])->name('user.profile.password');
Route::post('/profile/avatar', [UserProfileController::class, 'updateAvatar'])->name('user.profile.avatar');

// Host Property Flow
Route::middleware('web')->group(function () {
    Route::get('/host/start', [HostPropertyController::class, 'start'])->name('host.start');
    Route::get('/host/{room}/step/{step}', [HostPropertyController::class, 'step'])->name('host.step');
    Route::post('/host/{room}/save-field', [HostPropertyController::class, 'saveField'])->name('host.save-field');
    Route::post('/host/{room}/save-multiple', [HostPropertyController::class, 'saveMultiple'])->name('host.save-multiple');
    Route::post('/host/{room}/amenities', [HostPropertyController::class, 'saveAmenities'])->name('host.save-amenities');
    Route::post('/host/{room}/bedrooms', [HostPropertyController::class, 'saveBedrooms'])->name('host.save-bedrooms');
    Route::post('/host/{room}/upload-photo', [HostPropertyController::class, 'uploadPhoto'])->name('host.upload-photo');
    Route::post('/host/photo/{photo}/update-desc', [HostPropertyController::class, 'updatePhotoDesc'])->name('host.photo.update-desc');
    Route::delete('/host/photo/{photo}', [HostPropertyController::class, 'deletePhoto'])->name('host.photo.delete');
    Route::post('/host/{room}/upload-video', [HostPropertyController::class, 'uploadVideo'])->name('host.upload-video');
    Route::post('/host/{room}/enhancement', [HostPropertyController::class, 'addEnhancement'])->name('host.add-enhancement');
    Route::post('/host/{room}/enhancements-bulk', [HostPropertyController::class, 'saveEnhancements'])->name('host.save-enhancements');
    Route::post('/host/enhancement/{enhancement}/toggle', [HostPropertyController::class, 'toggleEnhancement'])->name('host.toggle-enhancement');
    Route::post('/host/{room}/calendar/toggle', [HostPropertyController::class, 'toggleCalendarDate'])->name('host.calendar.toggle');
    Route::post('/host/{room}/finish', [HostPropertyController::class, 'finish'])->name('host.finish');
});

// Forgot password
Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('forgot.password');
Route::post('/forgot-password/send-code', [ForgotPasswordController::class, 'sendCode'])->name('forgot.send');
Route::post('/forgot-password/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('forgot.verify');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('forgot.reset');

// User auth routes
Route::get('/auth', [AuthController::class, 'showAuthPage'])->name('auth');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Social login
Route::get('/auth/social/{provider}', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::any('/auth/social/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');

// Inactive account page
Route::get('/account-inactive', fn() => view('auth.inactive', ['name' => request('name', 'User')]))->name('inactive');

// Admin auth (no middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin protected routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    Route::get('/admins', [AdminController::class, 'admins'])->name('admins');
    Route::put('/admins/{user}/role', [AdminController::class, 'updateAdminRole'])->name('admins.role');

    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/site', [SettingsController::class, 'updateSite'])->name('settings.site');
    Route::post('/settings/map', [SettingsController::class, 'updateMap'])->name('settings.map');
    Route::post('/settings/fees', [SettingsController::class, 'updateFees'])->name('settings.fees');
    Route::post('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email');
    Route::post('/settings/email/toggle', [SettingsController::class, 'toggleEmail'])->name('settings.email.toggle');
    Route::post('/settings/recaptcha', [SettingsController::class, 'updateRecaptcha'])->name('settings.recaptcha');
    Route::post('/settings/recaptcha/toggle', [SettingsController::class, 'toggleRecaptcha'])->name('settings.recaptcha.toggle');
    Route::post('/settings/twilio', [SettingsController::class, 'updateTwilio'])->name('settings.twilio');
    Route::post('/settings/twilio/toggle', [SettingsController::class, 'toggleTwilio'])->name('settings.twilio.toggle');
    Route::post('/settings/social', [SettingsController::class, 'updateSocial'])->name('settings.social');
    Route::post('/settings/social/{provider}/toggle', [SettingsController::class, 'toggleSocial'])->name('settings.social.toggle');
    
    Route::post('/settings/payment', [SettingsController::class, 'updatePayment'])->name('settings.payment');
    Route::post('/settings/payment/{provider}/toggle', [SettingsController::class, 'togglePayment'])->name('settings.payment.toggle');
    
    Route::post('/users/{user}/toggle-status', [SettingsController::class, 'toggleUserStatus'])->name('users.toggle.status');

    Route::resource('property-types', PropertyTypeController::class);
    Route::resource('space-types', SpaceTypeController::class);
    Route::resource('amenities', AmenityController::class);
    Route::resource('room-beds', \App\Http\Controllers\Admin\RoomBedController::class);
    Route::resource('room-rules', \App\Http\Controllers\Admin\RoomRuleController::class);

    // Manage Reservations
    Route::get('/reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [\App\Http\Controllers\Admin\ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/send-email', [\App\Http\Controllers\Admin\ReservationController::class, 'sendEmail'])->name('reservations.send-email');

    // Manage Rooms
    Route::get('/rooms/settings', [\App\Http\Controllers\Admin\RoomController::class, 'settings'])->name('rooms.settings');
    Route::post('/rooms/settings', [\App\Http\Controllers\Admin\RoomController::class, 'updateSettings'])->name('rooms.settings.update');
    Route::delete('/rooms/photo/{id}', [\App\Http\Controllers\Admin\RoomController::class, 'deletePhoto'])->name('rooms.photo.delete');
    Route::post('/rooms/{room}/update-status', [\App\Http\Controllers\Admin\RoomController::class, 'updateStatus'])->name('rooms.update-status');
    Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class);

    // Subscription Plans
    Route::resource('subscription-plans', \App\Http\Controllers\Admin\SubscriptionPlanController::class);
    
    // User Subscriptions
    Route::get('/user-subscriptions', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'index'])->name('user-subscriptions.index');
});

// User Subscription Routes
Route::middleware('web')->group(function () {
    Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::match(['GET', 'POST'], '/subscriptions/{plan}/checkout', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');

    Route::get('/subscriptions/history', [\App\Http\Controllers\SubscriptionController::class, 'history'])->name('subscriptions.history');
    Route::post('/subscriptions/{id}/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribe'])->name('subscriptions.unsubscribe');

    // Subscription Payment Routes
    Route::post('/payment/subscription/paypal/initiate', [\App\Http\Controllers\SubscriptionPaymentController::class, 'initiatePaypal'])->name('payment.subscription.paypal.initiate');
    Route::get('/payment/subscription/paypal/success', [\App\Http\Controllers\SubscriptionPaymentController::class, 'paypalSuccess'])->name('payment.subscription.paypal.success');
    Route::get('/payment/subscription/paypal/cancel', [\App\Http\Controllers\SubscriptionPaymentController::class, 'paypalCancel'])->name('payment.subscription.paypal.cancel');

    Route::post('/payment/subscription/stripe/initiate', [\App\Http\Controllers\SubscriptionPaymentController::class, 'initiateStripe'])->name('payment.subscription.stripe.initiate');
    Route::get('/payment/subscription/stripe/success', [\App\Http\Controllers\SubscriptionPaymentController::class, 'stripeSuccess'])->name('payment.subscription.stripe.success');
    Route::get('/payment/subscription/stripe/cancel', [\App\Http\Controllers\SubscriptionPaymentController::class, 'stripeCancel'])->name('payment.subscription.stripe.cancel');

    Route::post('/payment/subscription/easebuzz/initiate', [\App\Http\Controllers\SubscriptionPaymentController::class, 'initiateEasebuzz'])->name('payment.subscription.easebuzz.initiate');
    Route::post('/payment/subscription/easebuzz/success', [\App\Http\Controllers\SubscriptionPaymentController::class, 'easebuzzSuccess'])->name('payment.subscription.easebuzz.success');
    Route::post('/payment/subscription/easebuzz/cancel', [\App\Http\Controllers\SubscriptionPaymentController::class, 'easebuzzCancel'])->name('payment.subscription.easebuzz.cancel');

    Route::post('/payment/subscription/razorpay/initiate', [\App\Http\Controllers\SubscriptionPaymentController::class, 'initiateRazorpay'])->name('payment.subscription.razorpay.initiate');
    Route::post('/payment/subscription/razorpay/success', [\App\Http\Controllers\SubscriptionPaymentController::class, 'razorpaySuccess'])->name('payment.subscription.razorpay.success');
    Route::post('/payment/subscription/razorpay/cancel', [\App\Http\Controllers\SubscriptionPaymentController::class, 'razorpayCancel'])->name('payment.subscription.razorpay.cancel');
});

// Wishlist Routes
Route::middleware('web')->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::get('/wishlist/groups', [\App\Http\Controllers\WishlistController::class, 'getGroups'])->name('wishlist.groups');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/group', [\App\Http\Controllers\WishlistController::class, 'deleteGroup'])->name('wishlist.delete_group');
});

