import { createI18n } from 'vue-i18n';
import Cookies from 'js-cookie';

const localeKey = 'user_locale';
const defaultLocale = 'en';

export function setupI18n(options = { locale: defaultLocale }) {
    const i18n = createI18n({
        legacy: false,
        locale: options.locale,
        fallbackLocale: defaultLocale,
        messages: {},
    });

    // Load the default locale messages
    loadLocaleMessages(i18n, options.locale).then(() => {});

    setI18nLanguage(i18n, options.locale);
    return i18n;
}

export async function loadLocaleMessages(i18n, locale) {
    // Check if the messages for the locale are already loaded
    if (!i18n.global.availableLocales.includes(locale)) {
        try {
            const messages = await import(`./lang/${locale}/index.js`);
            i18n.global.setLocaleMessage(locale, messages.default);
        } catch (error) {
            console.error(`Failed to load locale messages for ${locale}:`, error);
        }
    }
}

export function setI18nLanguage(i18n, locale) {
    if (i18n.mode === 'legacy') {
        i18n.global.locale = locale;
    } else {
        i18n.global.locale.value = locale;
    }

    document.querySelector('html').setAttribute('lang', locale);
}

export async function changeLocale(i18n, locale) {
    await loadLocaleMessages(i18n, locale);
    setI18nLanguage(i18n, locale);
    Cookies.set(localeKey, locale); // Persist the selected locale
}

export default setupI18n({
    locale: Cookies.get(localeKey) ?? defaultLocale,
});
