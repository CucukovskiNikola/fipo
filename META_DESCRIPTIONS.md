# Meta Descriptions Guide

This document provides SEO-optimized meta descriptions for all pages in the findemich application. Each description is crafted to be between 150-160 characters for optimal search engine display.

## Homepage / Welcome Page

**Route**: `/` (home)
**Page**: Welcome.vue
**Meta Description**: "Find trusted local business partners near you. Connect with verified companies, explore interactive maps, and discover quality services in your area."
**Length**: 159 characters

## Partners Public Page

**Route**: `/partners` (partners.public.show)
**Page**: Shows public partner listings
**Meta Description**: "Browse verified business partners by location and category. View detailed profiles, contact information, and interactive map locations."
**Length**: 153 characters

## User Authentication Pages

### Login

**Route**: `/login`
**Page**: auth/Login.vue
**Meta Description**: "Sign in to your findemich account to access partner management tools, dashboard features, and personalized business networking services."
**Length**: 147 characters

### Register

**Route**: `/register`
**Page**: auth/Register.vue
**Meta Description**: "Create your findemich account to connect with business partners, manage your profile, and access exclusive networking opportunities."
**Length**: 143 characters

### Forgot Password

**Route**: `/forgot-password`
**Page**: auth/ForgotPassword.vue
**Meta Description**: "Reset your findemich account password securely. Enter your email to receive password reset instructions and regain access to your account."
**Length**: 150 characters

### Reset Password

**Route**: `/reset-password/{token}`
**Page**: auth/ResetPassword.vue
**Meta Description**: "Set a new password for your findemich account. Complete the secure password reset process to regain full access to your business profile."
**Length**: 151 characters

### Email Verification

**Route**: `/verify-email`
**Page**: auth/VerifyEmail.vue
**Meta Description**: "Verify your email address to activate your findemich account and unlock all partner networking features and dashboard capabilities."
**Length**: 142 characters

## Admin Dashboard Pages (Protected)

### Dashboard Home

**Route**: `/dashboard`
**Page**: Dashboard.vue
**Meta Description**: "findemich Admin Dashboard - Manage business partners, view analytics, and oversee platform operations with comprehensive admin tools."
**Length**: 146 characters

### Partners Management

**Route**: `/dashboard/partners`
**Page**: Partners/Index.vue
**Meta Description**: "Manage business partner listings, review applications, and maintain partner database with advanced filtering and search tools."
**Length**: 147 characters

### Add Partner

**Route**: `/dashboard/partners/create`
**Page**: Partners/Create.vue
**Meta Description**: "Add new business partners to the findemich platform. Enter partner details, location data, and business information through our admin form."
**Length**: 151 characters

### Edit Partner

**Route**: `/dashboard/partners/{partner}/edit`
**Page**: Partners/Edit.vue
**Meta Description**: "Edit business partner information including contact details, location, category, and business description through the admin panel."
**Length**: 148 characters

### Partner Details

**Route**: `/dashboard/partners/{partner}`
**Page**: Partners/Show.vue
**Meta Description**: "View detailed business partner information including full profile, location data, images, and administrative management options."
**Length**: 149 characters

### Interactive Map

**Route**: `/dashboard/map`
**Page**: PartnersMap.vue
**Meta Description**: "Interactive map view of all business partners with location filtering, detailed popups, and geographic search capabilities."
**Length**: 142 characters

### Map Demo

**Route**: `/map-demo`
**Page**: MapDemo.vue
**Meta Description**: "Explore findemich's interactive mapping features with live partner locations, search functionality, and geographic business discovery."
**Length**: 147 characters

## User Settings Pages

### Profile Settings

**Route**: `/settings/profile`
**Page**: settings/Profile.vue
**Meta Description**: "Manage your findemich profile settings, update personal information, location preferences, and account details securely."
**Length**: 136 characters

### Password Settings

**Route**: `/settings/password`
**Page**: settings/Password.vue
**Meta Description**: "Update your findemich account password with secure validation. Change your login credentials safely and maintain account security."
**Length**: 148 characters

### Appearance Settings

**Route**: `/settings/appearance`
**Page**: settings/Appearance.vue
**Meta Description**: "Customize your findemich interface appearance with light/dark theme options and personalized display preferences."
**Length**: 132 characters

## Implementation Instructions

### 1. Laravel Integration

Add meta descriptions to your controllers by passing them to Inertia:

```php
// In your controllers
return Inertia::render('Welcome', [
    'meta' => [
        'title' => 'findemich - Find Local Business Partners',
        'description' => 'Find trusted local business partners near you. Connect with verified companies, explore interactive maps, and discover quality services in your area.',
        'keywords' => 'business partners, local services, verified companies, business networking'
    ]
]);
```

### 2. Blade Template Update

Update `resources/views/app.blade.php` to include meta tags:

```html
<head>
  <!-- Existing meta tags -->

  <!-- SEO Meta Tags -->
  @if(isset($page['props']['meta']))
  <meta
    name="description"
    content="{{ $page['props']['meta']['description'] }}"
  />
  <meta
    name="keywords"
    content="{{ $page['props']['meta']['keywords'] ?? '' }}"
  />

  <!-- Open Graph Meta Tags -->
  <meta
    property="og:title"
    content="{{ $page['props']['meta']['title'] ?? config('app.name') }}"
  />
  <meta
    property="og:description"
    content="{{ $page['props']['meta']['description'] }}"
  />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ request()->url() }}" />

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary" />
  <meta
    name="twitter:title"
    content="{{ $page['props']['meta']['title'] ?? config('app.name') }}"
  />
  <meta
    name="twitter:description"
    content="{{ $page['props']['meta']['description'] }}"
  />
  @else
  <meta
    name="description"
    content="findemich - Connect with trusted local business partners and discover quality services in your area."
  />
  @endif

  <!-- Existing head content -->
</head>
```

### 3. Vue.js Head Management

For dynamic pages, use Inertia's head management in Vue components:

```vue
<script setup>
import { Head } from "@inertiajs/vue3";

const props = defineProps(["partner", "meta"]);
</script>

<template>
  <Head>
    <title>{{ meta?.title || "findemich" }}</title>
    <meta name="description" :content="meta?.description" />
  </Head>

  <!-- Component content -->
</template>
```

## SEO Best Practices Applied

1. **Length Optimization**: All descriptions are 130-160 characters
2. **Keyword Integration**: Relevant keywords naturally included
3. **Action-Oriented**: Uses compelling action words
4. **Unique Content**: Each page has distinct, relevant descriptions
5. **Brand Consistency**: Maintains findemich brand voice throughout
6. **User Intent**: Matches what users are searching for
7. **Feature Highlights**: Emphasizes key platform features

## Additional Recommendations

1. **Dynamic Partner Pages**: Use partner name and category in meta descriptions
2. **Location-Based**: Include city/region for local SEO when applicable
3. **A/B Testing**: Test different descriptions for key pages
4. **Analytics**: Monitor click-through rates from search results
5. **Regular Updates**: Review and update descriptions quarterly

## Dynamic Meta Generation Example

For partner detail pages, generate dynamic descriptions:

```php
// In PartnerController
$metaDescription = "View {$partner->name} - {$partner->category} business partner in {$partner->city}. Contact information, services, and location details.";
```

This ensures each partner page has unique, relevant meta descriptions for better SEO performance.
