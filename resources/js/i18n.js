import { createI18n } from 'vue-i18n';
import vi from './locales/vi.json';
import th from './locales/th.json';
import en from './locales/en.json';
import lo from './locales/lo.json';

const i18n = createI18n({
    legacy: false, // Use Composition API
    locale: localStorage.getItem('locale') || 'vi', // Default locale
    fallbackLocale: 'en',
    messages: {
        vi,
        th,
        en,
        lo
    }
});

export default i18n;
