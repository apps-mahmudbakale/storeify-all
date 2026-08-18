# Storeify PWA Installation Guide

## Overview
Storeify is now a Progressive Web App (PWA), which means it can be installed on mobile and desktop devices and work offline with cached assets.

## PWA Features Enabled

✅ **Offline Support** - Service worker caches essential assets
✅ **Installable** - Add to home screen on iOS/Android
✅ **App-like Experience** - Standalone display mode
✅ **Custom Shortcuts** - Quick access to New Sale, Products, Reports
✅ **Theme Colors** - Custom branding and status bar

## Installation Instructions

### Android (Chrome/Edge)

1. **Open Storeify** in Chrome or Edge browser
   - Navigate to: `https://your-domain.com/app/dashboard`

2. **Install Prompt** (should appear automatically)
   - Tap the **"Install"** or **"Add to Home Screen"** prompt at the bottom
   - Or tap the menu (⋮) → **"Install app"**

3. **Confirm Installation**
   - App will appear on your home screen as **"Storeify"**
   - Tap the icon to launch the app

4. **Features Once Installed**
   - App runs in standalone mode (no address bar)
   - Theme colors match Storeify branding
   - Quick shortcuts in app drawer
   - Offline access to cached pages

### iOS (Safari)

1. **Open Storeify in Safari**
   - Navigate to: `https://your-domain.com/app/dashboard`

2. **Share Menu**
   - Tap the **Share** button (square with arrow)
   - Scroll down and select **"Add to Home Screen"**

3. **Customize (Optional)**
   - Edit the name (default: "Storeify")
   - Edit the URL if needed
   - Tap **"Add"**

4. **Launch**
   - App appears on home screen
   - Tap icon to open
   - Runs in full-screen mode with custom status bar

### Desktop (Chrome/Edge/Firefox)

1. **Open Storeify**
   - Navigate to your Storeify domain

2. **Install Prompt**
   - Look for install icon in address bar (⊕ icon)
   - Or use **Menu (⋮)** → **"Install Storeify"**

3. **Confirm**
   - Desktop app will be installed in your applications
   - Creates a start menu entry and desktop shortcut

## Offline Functionality

### What Works Offline
✅ Static assets (CSS, images, logo)
✅ Previously loaded pages (from cache)
✅ UI components and navigation
✅ LocalStorage data

### What Requires Internet
❌ Dashboard (real-time data)
❌ Creating new sales
❌ Fetching products
❌ Reports and analytics

**Note:** When offline, the app will attempt to load from cache but will show a "503 Service Unavailable" message for pages that require server data.

## Testing the PWA

### Verify Installation

**Desktop (Chrome DevTools):**
1. Open DevTools (F12)
2. Go to **Application** tab
3. Check **Manifest** section - should show app metadata
4. Check **Service Worker** - should show registered and active

**Mobile:**
- Settings → Apps → Look for "Storeify"
- App should appear with Storeify icon

### Test Offline Mode

1. **Install the app first** (as instructed above)

2. **Go Online Mode**
   - Open app normally
   - Navigate to a page (e.g., Products)

3. **Enable Offline**
   - Android: Settings → WiFi → Turn off WiFi & disable mobile data
   - iOS: Settings → WiFi → Turn off WiFi & enable Airplane Mode
   - Desktop: DevTools → Network → Offline

4. **Verify Cached Content**
   - Previously loaded pages should display
   - Logo and images should load from cache
   - Navigation should work

5. **Go Back Online**
   - Re-enable connections
   - Refresh page to get live data

## App Shortcuts

The installed app includes quick access shortcuts:

1. **New Sale** - Jump directly to sales creation
2. **Products** - View and manage products
3. **Reports** - Access reporting dashboard

**How to Access Shortcuts:**
- **Android:** Long-press app icon (if launcher supports it)
- **iOS:** Not supported (Apple limitation)
- **Desktop:** Right-click app icon in taskbar/start menu

## Troubleshooting

### PWA Not Installing?

**Android:**
- Ensure HTTPS is enabled
- Clear browser cache: Settings → Storage → Clear cache
- Try in different browser (Chrome recommended)

**iOS:**
- Must use Safari (not Chrome)
- Requires HTTPS
- Update iOS to latest version
- Check website has valid manifest.json

**Desktop:**
- Use Chrome, Edge, or modern Firefox
- Must have HTTPS
- Check browser's install button in address bar

### Service Worker Not Registering?

1. Open DevTools (F12)
2. Go to Console tab
3. Check for errors related to service-worker.js
4. Common issues:
   - Invalid manifest.json
   - Service worker file has syntax errors
   - HTTPS not enabled

### App Crashes or Won't Load?

1. **Clear app data:**
   - Android: Settings → Apps → Storeify → Storage → Clear Cache
   - Desktop: Uninstall and reinstall

2. **Clear service worker:**
   - DevTools → Application → Service Workers → Unregister
   - Refresh page

3. **Check internet connection**
   - App needs internet to sync data

## Important Notes

⚠️ **HTTPS Required** - PWA requires HTTPS to work (for security)

⚠️ **First Visit** - Service worker installs on first visit; full offline may take a few minutes

⚠️ **Cache Updates** - Cache versions clear automatically when app updates

⚠️ **Storage Limits** - Browser may limit cache storage (typically 50MB+)

## Files Included

- **manifest.json** - App metadata and configuration
- **service-worker.js** - Offline support and caching logic
- **Updated layouts** - Meta tags for PWA support

## Support

If you encounter issues:
1. Check browser console for errors (DevTools → Console)
2. Verify HTTPS is enabled
3. Clear browser cache and try again
4. Try installing on a different device/browser
5. Check that manifest.json is accessible at `/manifest.json`

---

**Storeify PWA** - Mobile-first inventory management for restaurants and medical facilities.
