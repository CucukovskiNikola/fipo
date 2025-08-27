import { ref, reactive, readonly } from 'vue'

export const COOKIE_TYPES = {
  ESSENTIAL: 'essential',
  FUNCTIONAL: 'functional',
  ANALYTICS: 'analytics',
  MARKETING: 'marketing'
}

export const CONSENT_STATUS = {
  PENDING: 'pending',
  ACCEPTED: 'accepted',
  DECLINED: 'declined',
  PARTIAL: 'partial'
}

const STORAGE_KEYS = {
  CONSENT: 'cookie_consent',
  PREFERENCES: 'cookie_preferences',
  AUDIT_TRAIL: 'cookie_audit_trail'
}

const CONSENT_VERSION = '2.0'
const CONSENT_EXPIRY_DAYS = 365

// Global state
const consentStatus = ref(CONSENT_STATUS.PENDING)
const preferences = reactive({
  [COOKIE_TYPES.ESSENTIAL]: true, // Always true
  [COOKIE_TYPES.FUNCTIONAL]: false,
  [COOKIE_TYPES.ANALYTICS]: false,
  [COOKIE_TYPES.MARKETING]: false
})

const auditTrail = ref([])

// Cookie utilities
export const cookieUtils = {
  set: (name, value, days = 30, options = {}) => {
    const expires = new Date()
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000))
    
    const cookieOptions = {
      expires: expires.toUTCString(),
      path: '/',
      secure: window.location.protocol === 'https:',
      sameSite: 'Lax',
      ...options
    }
    
    let cookieString = `${name}=${encodeURIComponent(value)}`
    Object.entries(cookieOptions).forEach(([key, val]) => {
      if (val === true) {
        cookieString += `; ${key}`
      } else if (val !== false) {
        cookieString += `; ${key}=${val}`
      }
    })
    
    document.cookie = cookieString
    
    // Log to audit trail
    logCookieAction('set', name, { expires: cookieOptions.expires })
  },
  
  get: (name) => {
    const nameEQ = `${name}=`
    const cookies = document.cookie.split(';')
    
    for (let cookie of cookies) {
      let c = cookie.trim()
      if (c.indexOf(nameEQ) === 0) {
        return decodeURIComponent(c.substring(nameEQ.length))
      }
    }
    return null
  },
  
  delete: (name, options = {}) => {
    const deleteOptions = {
      expires: 'Thu, 01 Jan 1970 00:00:00 UTC',
      path: '/',
      ...options
    }
    
    let cookieString = `${name}=`
    Object.entries(deleteOptions).forEach(([key, val]) => {
      if (val === true) {
        cookieString += `; ${key}`
      } else if (val !== false) {
        cookieString += `; ${key}=${val}`
      }
    })
    
    document.cookie = cookieString
    
    // Log to audit trail
    logCookieAction('delete', name)
  },
  
  list: () => {
    const cookies = {}
    document.cookie.split(';').forEach(cookie => {
      const [name, value] = cookie.trim().split('=')
      if (name && value) {
        cookies[name] = decodeURIComponent(value)
      }
    })
    return cookies
  }
}

// Audit trail functions
const logCookieAction = (action, name, metadata = {}) => {
  const entry = {
    timestamp: new Date().toISOString(),
    action,
    cookieName: name,
    metadata,
    userAgent: navigator.userAgent,
    url: window.location.href
  }
  
  auditTrail.value.unshift(entry)
  
  // Keep only last 100 entries
  if (auditTrail.value.length > 100) {
    auditTrail.value = auditTrail.value.slice(0, 100)
  }
  
  // Save to localStorage
  localStorage.setItem(STORAGE_KEYS.AUDIT_TRAIL, JSON.stringify(auditTrail.value))
}

// Google Analytics integration
const initializeGoogleAnalytics = (measurementId) => {
  if (!measurementId || !preferences[COOKIE_TYPES.ANALYTICS]) return
  
  // Load gtag script
  const script = document.createElement('script')
  script.async = true
  script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`
  document.head.appendChild(script)
  
  // Initialize gtag
  window.dataLayer = window.dataLayer || []
  function gtag() { dataLayer.push(arguments) }
  window.gtag = gtag
  
  gtag('js', new Date())
  gtag('config', measurementId, {
    anonymize_ip: true,
    cookie_flags: 'max-age=7200;secure;samesite=none'
  })
  
  logCookieAction('analytics_enabled', 'google_analytics', { measurementId })
}

// Facebook Pixel integration
const initializeFacebookPixel = (pixelId) => {
  if (!pixelId || !preferences[COOKIE_TYPES.MARKETING]) return
  
  !function(f,b,e,v,n,t,s) {
    if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
  
  fbq('init', pixelId)
  fbq('track', 'PageView')
  
  logCookieAction('marketing_enabled', 'facebook_pixel', { pixelId })
}

// Main cookie manager composable
export const useCookieManager = () => {
  
  const loadStoredData = () => {
    // Load consent
    const storedConsent = localStorage.getItem(STORAGE_KEYS.CONSENT)
    if (storedConsent) {
      try {
        const consent = JSON.parse(storedConsent)
        if (consent.version === CONSENT_VERSION) {
          consentStatus.value = consent.status
          Object.assign(preferences, consent.preferences)
        }
      } catch (e) {
        console.error('Error parsing stored consent:', e)
      }
    }
    
    // Load audit trail
    const storedAudit = localStorage.getItem(STORAGE_KEYS.AUDIT_TRAIL)
    if (storedAudit) {
      try {
        auditTrail.value = JSON.parse(storedAudit)
      } catch (e) {
        console.error('Error parsing audit trail:', e)
      }
    }
  }
  
  const saveConsent = (status, prefs = null) => {
    const consentData = {
      status,
      preferences: prefs || preferences,
      version: CONSENT_VERSION,
      timestamp: new Date().toISOString(),
      expiresAt: new Date(Date.now() + (CONSENT_EXPIRY_DAYS * 24 * 60 * 60 * 1000)).toISOString(),
      userAgent: navigator.userAgent,
      ipHash: null // Should be set by backend
    }
    
    localStorage.setItem(STORAGE_KEYS.CONSENT, JSON.stringify(consentData))
    consentStatus.value = status
    
    if (prefs) {
      Object.assign(preferences, prefs)
    }
    
    // Log consent change
    logCookieAction('consent_updated', 'consent_manager', {
      status,
      preferences: prefs || preferences
    })
    
    // Apply consent
    applyConsent()
  }
  
  const applyConsent = () => {
    // Clean up non-essential cookies if declined
    if (!preferences[COOKIE_TYPES.ANALYTICS]) {
      cleanupAnalyticsCookies()
    }
    
    if (!preferences[COOKIE_TYPES.MARKETING]) {
      cleanupMarketingCookies()
    }
    
    if (!preferences[COOKIE_TYPES.FUNCTIONAL]) {
      cleanupFunctionalCookies()
    }
    
    // Initialize allowed services
    if (preferences[COOKIE_TYPES.ANALYTICS]) {
      initializeGoogleAnalytics(window.GA_MEASUREMENT_ID)
    }
    
    if (preferences[COOKIE_TYPES.MARKETING]) {
      initializeFacebookPixel(window.FB_PIXEL_ID)
    }
  }
  
  const cleanupAnalyticsCookies = () => {
    const analyticsCookies = ['_ga', '_ga_*', '_gid', '_gat', '_gat_*', '__utma', '__utmb', '__utmc', '__utmt', '__utmz']
    analyticsCookies.forEach(pattern => {
      if (pattern.includes('*')) {
        const prefix = pattern.replace('*', '')
        Object.keys(cookieUtils.list()).forEach(name => {
          if (name.startsWith(prefix)) {
            cookieUtils.delete(name)
          }
        })
      } else {
        cookieUtils.delete(pattern)
      }
    })
  }
  
  const cleanupMarketingCookies = () => {
    const marketingCookies = ['_fbp', '_fbc', 'fr', 'tr', '__Secure-*', '__Host-*']
    marketingCookies.forEach(pattern => {
      if (pattern.includes('*')) {
        const prefix = pattern.replace('*', '')
        Object.keys(cookieUtils.list()).forEach(name => {
          if (name.startsWith(prefix)) {
            cookieUtils.delete(name)
          }
        })
      } else {
        cookieUtils.delete(pattern)
      }
    })
  }
  
  const cleanupFunctionalCookies = () => {
    const functionalCookies = ['theme', 'language', 'preferences']
    functionalCookies.forEach(name => {
      cookieUtils.delete(name)
    })
  }
  
  const acceptAllCookies = () => {
    const newPrefs = {
      [COOKIE_TYPES.ESSENTIAL]: true,
      [COOKIE_TYPES.FUNCTIONAL]: true,
      [COOKIE_TYPES.ANALYTICS]: true,
      [COOKIE_TYPES.MARKETING]: true
    }
    
    saveConsent(CONSENT_STATUS.ACCEPTED, newPrefs)
  }
  
  const declineAllCookies = () => {
    const newPrefs = {
      [COOKIE_TYPES.ESSENTIAL]: true, // Always true
      [COOKIE_TYPES.FUNCTIONAL]: false,
      [COOKIE_TYPES.ANALYTICS]: false,
      [COOKIE_TYPES.MARKETING]: false
    }
    
    saveConsent(CONSENT_STATUS.DECLINED, newPrefs)
  }
  
  const saveCustomPreferences = (customPrefs) => {
    const newPrefs = {
      [COOKIE_TYPES.ESSENTIAL]: true, // Always true
      ...customPrefs
    }
    
    const hasAnyAccepted = Object.entries(newPrefs)
      .some(([key, value]) => key !== COOKIE_TYPES.ESSENTIAL && value === true)
    
    const status = hasAnyAccepted ? CONSENT_STATUS.PARTIAL : CONSENT_STATUS.DECLINED
    saveConsent(status, newPrefs)
  }
  
  const withdrawConsent = () => {
    // Clear all stored data
    localStorage.removeItem(STORAGE_KEYS.CONSENT)
    localStorage.removeItem(STORAGE_KEYS.PREFERENCES)
    
    // Reset to defaults
    consentStatus.value = CONSENT_STATUS.PENDING
    Object.assign(preferences, {
      [COOKIE_TYPES.ESSENTIAL]: true,
      [COOKIE_TYPES.FUNCTIONAL]: false,
      [COOKIE_TYPES.ANALYTICS]: false,
      [COOKIE_TYPES.MARKETING]: false
    })
    
    // Clean up all non-essential cookies
    cleanupAnalyticsCookies()
    cleanupMarketingCookies()
    cleanupFunctionalCookies()
    
    logCookieAction('consent_withdrawn', 'consent_manager')
  }
  
  const isConsentExpired = () => {
    const storedConsent = localStorage.getItem(STORAGE_KEYS.CONSENT)
    if (!storedConsent) return true
    
    try {
      const consent = JSON.parse(storedConsent)
      const expiresAt = new Date(consent.expiresAt)
      return new Date() > expiresAt
    } catch (e) {
      return true
    }
  }
  
  const getDaysUntilExpiry = () => {
    const storedConsent = localStorage.getItem(STORAGE_KEYS.CONSENT)
    if (!storedConsent) return 0
    
    try {
      const consent = JSON.parse(storedConsent)
      const expiresAt = new Date(consent.expiresAt)
      const now = new Date()
      const diffTime = expiresAt.getTime() - now.getTime()
      return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    } catch (e) {
      return 0
    }
  }
  
  const getActiveCookies = () => {
    return cookieUtils.list()
  }
  
  const getCookiesByCategory = () => {
    const allCookies = cookieUtils.list()
    const categorized = {
      [COOKIE_TYPES.ESSENTIAL]: [],
      [COOKIE_TYPES.FUNCTIONAL]: [],
      [COOKIE_TYPES.ANALYTICS]: [],
      [COOKIE_TYPES.MARKETING]: [],
      unknown: []
    }
    
    // Cookie categorization patterns
    const patterns = {
      [COOKIE_TYPES.ESSENTIAL]: [
        'PHPSESSID', 'csrf_token', 'laravel_session', '_token',
        'cookie_consent', 'cookie_preferences'
      ],
      [COOKIE_TYPES.FUNCTIONAL]: [
        'theme', 'language', 'preferences', 'timezone'
      ],
      [COOKIE_TYPES.ANALYTICS]: [
        '_ga', '_ga_', '_gid', '_gat', '__utm', '__gads'
      ],
      [COOKIE_TYPES.MARKETING]: [
        '_fbp', '_fbc', 'fr', 'tr', '__Secure-', '__Host-'
      ]
    }
    
    Object.entries(allCookies).forEach(([name, value]) => {
      let categorized_cookie = false
      
      Object.entries(patterns).forEach(([category, cookiePatterns]) => {
        if (cookiePatterns.some(pattern => name.includes(pattern))) {
          categorized[category].push({ name, value })
          categorized_cookie = true
        }
      })
      
      if (!categorized_cookie) {
        categorized.unknown.push({ name, value })
      }
    })
    
    return categorized
  }
  
  // Initialize on first use
  loadStoredData()
  
  // Check if consent is expired
  if (isConsentExpired() && consentStatus.value !== CONSENT_STATUS.PENDING) {
    withdrawConsent()
  }
  
  return {
    // State
    consentStatus: readonly(consentStatus),
    preferences: readonly(preferences),
    auditTrail: readonly(auditTrail),
    
    // Actions
    acceptAllCookies,
    declineAllCookies,
    saveCustomPreferences,
    withdrawConsent,
    
    // Utilities
    cookieUtils,
    getActiveCookies,
    getCookiesByCategory,
    getDaysUntilExpiry,
    isConsentExpired,
    
    // Constants
    COOKIE_TYPES,
    CONSENT_STATUS
  }
}