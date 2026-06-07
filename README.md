<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
  <h1>🏨 Hotel Host Platform</h1>
  <p><strong>A fully-featured, modern accommodation booking and hosting platform built with Laravel.</strong></p>

  <p>
    <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Status-Active-success?style=for-the-badge" alt="Status">
  </p>
</div>

<hr>

## 🌟 Overview

**Hotel Host** is a comprehensive, multi-tenant marketplace connecting property owners (Hosts) with travelers (Guests). The platform is architected to handle complex operational flows including dynamic pricing algorithms, subscription-based hosting tiers, granular dynamic settings, and multi-gateway payment processing.

Featuring a beautiful, responsive user interface with **built-in Dark Mode**, the application is engineered for performance, security, and scalability.

---

## 🏗️ Deep Dive: Core System Concepts & Flows

### 1. The Dynamic Pricing Engine
The booking calculation engine is highly dynamic, processing multiple layers of rules to calculate the final price in real-time. This ensures hosts can maximize revenue based on seasonality, guest count, and length of stay.

```mermaid
graph TD
    A[Start Calculation] --> B[Fetch Base Room Price]
    B --> C{Custom Calendar Rules?}
    C -->|Yes| D[Apply Date-Specific Override/Weekend Price]
    C -->|No| E[Use Base Price]
    D & E --> F[Calculate Stay Total for N Nights]
    F --> G{Extra Guests?}
    G -->|Yes| H[Add Extra Guest Fee per Night]
    G -->|No| I[Proceed]
    H & I --> J{Length of Stay Discounts?}
    J -->|Weekly| K[Apply 7+ Days Discount %]
    J -->|Monthly| L[Apply 28+ Days Discount %]
    J -->|None| M[Proceed]
    K & L & M --> N[Subtotal calculated]
    N --> O[Add Cleaning Fee]
    O --> P[Add Platform Service Fee %]
    P --> Q[Add Security Deposit]
    Q --> R[Calculate Taxes]
    R --> S[Final Total Price Generated]
```

### 2. Multi-Channel Authentication & Session Flow
The platform moves beyond standard Laravel Auth, implementing a highly modular authentication flow. It supports direct credential logins, passwordless OTP (Twilio), and OAuth (Google, Facebook, Apple).

```mermaid
sequenceDiagram
    participant User
    participant AuthSystem
    participant Twilio/OAuth
    participant LaravelSession
    
    User->>AuthSystem: Selects Login Method
    
    alt Phone (OTP)
        AuthSystem->>Twilio/OAuth: Request SMS OTP
        Twilio/OAuth-->>User: Sends 6-digit Code
        User->>AuthSystem: Enters Code
    else Social Login
        AuthSystem->>Twilio/OAuth: Redirect to Provider
        Twilio/OAuth-->>AuthSystem: Returns User Payload
    else Email/Password
        User->>AuthSystem: Submits Credentials
    end
    
    AuthSystem->>AuthSystem: Verify Active Status
    AuthSystem->>LaravelSession: Set custom session('user_id')
    AuthSystem->>LaravelSession: Auth::login($user) for native guard
    LaravelSession-->>User: Authenticated Dashboard Access
```

### 3. The Host Subscription Lifecycle
To list properties, users can upgrade to Host status via a tiered subscription model. The system tracks subscription states, duration limits, and automatic capability revoking upon expiration.

```mermaid
stateDiagram-v2
    [*] --> Guest
    Guest --> Checkout : Selects Subscription Plan
    Checkout --> PaymentGateway : Process (Stripe/PayPal/Easebuzz/Razorpay)
    PaymentGateway --> ActiveSubscription : Payment Success
    
    state ActiveSubscription {
        [*] --> UnlocksHosting
        UnlocksHosting --> ReducedFees
        ReducedFees --> PrioritySupport
    }
    
    ActiveSubscription --> Expired : Duration Lapses
    ActiveSubscription --> Cancelled : User Unsubscribes
    Expired --> Checkout : Renews
    Cancelled --> Checkout : Renews
```

### 4. Interactive Property Listing Pipeline
Creating a listing is a massive data-entry task. Hotel Host breaks this down into a highly modular, interactive 7-step wizard that saves progress asynchronously.

```mermaid
graph LR
    A[Start Hosting] -->|Step 1| B[Basic Info & Type]
    B -->|Step 2| C[Bedrooms, Beds & Bathrooms]
    C -->|Step 3| D[Location Map & Address]
    D -->|Step 4| E[Amenities Taxonomy]
    E -->|Step 5| F[Photos & Videos Upload]
    F -->|Step 6| G[House Rules & Enhancements]
    G -->|Step 7| H[Pricing Engine Setup & Publish]
```

### 5. Dynamic Configuration System (Admin)
The platform is built to be manageable without code changes. Admins control a unified "Settings" JSON/Database architecture.

* **Payment Toggles:** Turn Stripe, PayPal, Razorpay, or Easebuzz on/off instantly. If disabled, they hide from the UI.
* **Social Auth Toggles:** Enable or disable Apple, Google, or Facebook login.
* **Feature Toggles:** Turn Twilio SMS or Google reCAPTCHA on/off dynamically. The app seamlessly falls back to local processing (e.g. dummy OTPs) if APIs are disabled.

---

## 🚀 Core Features by Role

### 👤 For Guests
- **Smart Search & Discovery**: Browse destinations, filter by amenities, price, and property types.
- **Wishlists & Grouping**: Save and categorize favorite properties into custom folders.
- **Trip Management**: View detailed itineraries, download PDF receipts, and manage upcoming reservations.

### 🏠 For Hosts
- **Calendar Management**: Block out dates, set custom prices for holidays, or make dates unavailable.
- **Earnings Tracking**: Monitor revenue minus platform fees (which dynamically reduce if the host is on a premium subscription).
- **Property Enhancements**: Upsell guests on extra features (e.g., airport pickup, extra cleaning).

### 🛡️ For Administrators
- **Centralized Dashboard**: Complete oversight over users, properties, and revenue.
- **Taxonomy Management**: Create and manage Property Types, Space Types, and global Amenities dynamically.
- **Subscription Management**: Configure host subscription tiers, pricing, and capability limits.

---

## 🛠️ Technology Stack

| Category | Technologies |
|----------|-------------|
| **Backend Framework** | Laravel 11.x, PHP 8.2+ |
| **Database** | MySQL, Eloquent ORM |
| **Frontend Rendering**| Blade Templates, Vanilla CSS (Design System) |
| **State & Interactivity**| Vanilla JavaScript, AJAX Fetch API |
| **Authentication** | Laravel Auth, Custom Session Sync, Laravel Socialite |
| **APIs/Services**| Twilio (SMS), Google Maps, reCAPTCHA v3 |

---

## 🎨 UI/UX Philosophy
- **Glassmorphism & Gradients**: Modern aesthetic utilizing subtle shadows, frosted glass effects, and vibrant gradient accents to convey a premium feel.
- **First-Class Theme Support**: Deep integration for Light and Dark modes, utilizing CSS custom properties for instant toggling without page reloads.
- **Micro-interactions**: Hover states, smooth transitions, and elegant CSS tooltips guide user behavior without cluttering the interface.

---

## ⚙️ Local Development Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd CRUD-APP
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Environment Configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Update your `.env` file with your database credentials and API keys (Stripe, Twilio, etc).*

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Link Storage:**
   ```bash
   php artisan storage:link
   ```

6. **Start the Development Server:**
   ```bash
   php artisan serve
   ```
   *Visit `http://localhost:8000` in your browser.*

---
<div align="center">
  <p>Built with ❤️ using Laravel</p>
</div>
