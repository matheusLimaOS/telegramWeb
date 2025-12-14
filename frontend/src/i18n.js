import i18n from 'i18next'
import LanguageDetector from 'i18next-browser-languagedetector'
import { initReactI18next } from 'react-i18next'

// Importe seus arquivos de tradução (mocks)
import en from './locales/en/translation.json'
import pt from './locales/pt/translation.json'

i18n
  // Detecta a linguagem do usuário (navegador)
  .use(LanguageDetector)
  // Conecta i18next com React
  .use(initReactI18next)
  // Inicializa o i18next
  .init({
    // Recurso de tradução
    resources: {
      en: {
        translation: en,
      },
      pt: {
        translation: pt,
      },
    },
    // Linguagem padrão caso a detecção falhe
    fallbackLng: 'pt',
    // Namespace padrão (você pode ter vários: comum, cabeçalho, rodapé...)
    defaultNS: 'translation',

    interpolation: {
      escapeValue: false, // O React já faz isso por padrão
    },
    // Opções de detecção de linguagem (salvar a preferência do usuário)
    detection: {
      order: ['localStorage', 'navigator'],
      caches: ['localStorage'],
    },
  })

export default i18n
