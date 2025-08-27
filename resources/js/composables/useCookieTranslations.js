import { computed } from 'vue'
import { useTranslations } from './useTranslations'

// Fallback translations in case the server translations aren't loaded
const fallbackTranslations = {
  en: {
    title: 'We value your privacy',
    description: 'This website uses cookies to ensure you get the best experience on our website. We use essential cookies for site functionality, analytics cookies to understand how you interact with our site, and marketing cookies for personalized content.',
    accept: 'Accept All Cookies',
    decline: 'Reject All',
    settings: 'Manage Preferences',
    save_preferences: 'Save My Preferences',
    essential_cookies: 'Strictly Necessary Cookies',
    analytics_cookies: 'Performance & Analytics Cookies',
    marketing_cookies: 'Targeting & Marketing Cookies',
    functional_cookies: 'Functional Cookies',
    essential_description: 'These cookies are essential for the proper functioning of our website. Without these cookies, the website would not work properly. They cannot be switched off.',
    analytics_description: 'These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site. All information these cookies collect is aggregated.',
    marketing_description: 'These cookies may be set through our site by our advertising partners. They may be used to build a profile of your interests and show you relevant adverts.',
    functional_description: 'These cookies enable the website to provide enhanced functionality and personalization, such as remembering your preferences.',
    cookie_policy: 'Cookie Policy',
    privacy_policy: 'Privacy Policy',
    withdraw_consent: 'Withdraw Consent',
    do_not_sell: 'Do Not Sell My Personal Information',
    powered_by: 'Cookie consent by',
    last_updated: 'Last updated',
    expires_in: 'Consent expires in',
    days: 'days',
    view_cookies: 'View Active Cookies',
    always_active: 'Always Active',
    on: 'On',
    off: 'Off',
    withdraw_consent_confirm: 'Are you sure you want to withdraw your cookie consent? This will delete all non-essential cookies and reset your preferences.'
  },
  de: {
    title: 'Wir schätzen Ihre Privatsphäre',
    description: 'Diese Website verwendet Cookies, um Ihnen die bestmögliche Erfahrung auf unserer Website zu bieten. Wir verwenden wesentliche Cookies für die Website-Funktionalität, Analyse-Cookies um zu verstehen, wie Sie mit unserer Website interagieren, und Marketing-Cookies für personalisierte Inhalte.',
    accept: 'Alle Cookies akzeptieren',
    decline: 'Alle ablehnen',
    settings: 'Einstellungen verwalten',
    save_preferences: 'Meine Einstellungen speichern',
    essential_cookies: 'Unbedingt erforderliche Cookies',
    analytics_cookies: 'Leistungs- & Analyse-Cookies',
    marketing_cookies: 'Targeting- & Marketing-Cookies',
    functional_cookies: 'Funktionale Cookies',
    essential_description: 'Diese Cookies sind für das ordnungsgemäße Funktionieren unserer Website unerlässlich. Ohne diese Cookies würde die Website nicht richtig funktionieren. Sie können nicht ausgeschaltet werden.',
    analytics_description: 'Diese Cookies ermöglichen es uns, Besuche und Verkehrsquellen zu zählen, damit wir die Leistung unserer Website messen und verbessern können. Alle von diesen Cookies gesammelten Informationen sind aggregiert.',
    marketing_description: 'Diese Cookies können über unsere Website von unseren Werbepartnern gesetzt werden. Sie können verwendet werden, um ein Profil Ihrer Interessen zu erstellen und Ihnen relevante Werbung zu zeigen.',
    functional_description: 'Diese Cookies ermöglichen der Website erweiterte Funktionalität und Personalisierung, wie z.B. das Merken Ihrer Präferenzen.',
    cookie_policy: 'Cookie-Richtlinie',
    privacy_policy: 'Datenschutzrichtlinie',
    withdraw_consent: 'Einwilligung widerrufen',
    do_not_sell: 'Meine persönlichen Daten nicht verkaufen',
    powered_by: 'Cookie-Zustimmung von',
    last_updated: 'Zuletzt aktualisiert',
    expires_in: 'Zustimmung läuft ab in',
    days: 'Tagen',
    view_cookies: 'Aktive Cookies anzeigen',
    always_active: 'Immer aktiv',
    on: 'An',
    off: 'Aus',
    withdraw_consent_confirm: 'Sind Sie sicher, dass Sie Ihre Cookie-Einwilligung widerrufen möchten? Dadurch werden alle nicht wesentlichen Cookies gelöscht und Ihre Präferenzen zurückgesetzt.'
  }
}

export const useCookieTranslations = () => {
  const { trans, locale } = useTranslations()

  const currentLocale = computed(() => {
    return locale.value || 'en'
  })

  const cookieTrans = (key) => {
    try {
      // Try to get from server translations first
      const serverTranslation = trans(`cookies.${key}`)
      
      // If we get the key back unchanged, it means the translation wasn't found
      if (serverTranslation === `cookies.${key}` || serverTranslation === key) {
        // Fallback to our hardcoded translations
        const fallback = fallbackTranslations[currentLocale.value] || fallbackTranslations.en
        return fallback[key] || key
      }
      
      return serverTranslation
    } catch (error) {
      console.warn('Cookie translation error:', error)
      // Fallback to our hardcoded translations
      const fallback = fallbackTranslations[currentLocale.value] || fallbackTranslations.en
      return fallback[key] || key
    }
  }

  return {
    cookieTrans,
    currentLocale
  }
}