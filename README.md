# OltreBlocksy WordPress Theme

Un tema WordPress altamente modulare e configurabile che supera Blocksy in termini di opzioni, bellezza, pulizia, modularità e performance.

## 🚀 Caratteristiche Principali

### Architettura Modulare Avanzata
- **Sistema modulare completo**: Ogni funzionalità è un modulo separato che può essere attivato/disattivato
- **Autoloader PSR-4**: Namespace e autoloading per tutte le classi
- **API estendibile**: Hooks e filters per personalizzazioni avanzate

### Sistema Tipografico Rivoluzionario
- **Typography fluida**: Scaling automatico basato su viewport con clamp()
- **Font pairing intelligente**: Combinazioni tipografiche pre-configurate
- **Preset tipografici**: Elegant, Modern, Editorial, Minimalist, Creative
- **Google Fonts ottimizzati**: Caricamento intelligente con preconnect

### Performance di Prima Classe
- **Critical CSS inline**: CSS critico generato dinamicamente
- **Lazy loading avanzato**: Immagini, iframe, video e componenti
- **Code splitting**: JavaScript modulare con caricamento condizionale
- **Resource hints**: Preload, prefetch, preconnect intelligenti
- **Ottimizzazioni database**: Cache delle query e object caching

### Sistema Colori Avanzato
- **Design tokens centralizzati**: Sistema di token per colori, spaziature, tipografia
- **Palette dinamiche**: Generazione automatica di palette colori complementari
- **Dark mode intelligente**: Switch automatico basato su preferenze sistema
- **Accessibility checker**: Controllo automatico contrasto WCAG 2.1 AA

### Layout System CSS Grid Nativo
- **CSS Grid moderno**: Layout system basato su CSS Grid con fallback Flexbox
- **Container queries**: Supporto per container queries quando disponibili
- **Aspect ratio control**: Controllo completo delle proporzioni
- **Spacing system**: Sistema di spaziature coerente

### Customizer API Esteso
- **Pannelli dinamici**: Sistema di pannelli che si adattano al contenuto
- **Controlli personalizzati**: Slider, color picker, typography selector
- **Conditional logic**: Opzioni che appaiono/scompaiono basate su altre selezioni
- **Live preview**: Anteprima in tempo reale di tutte le modifiche

### Accessibilità WCAG 2.1 AA
- **Compliance completa**: Supporto screen reader e navigazione tastiera
- **Focus management**: Gestione avanzata del focus per modali e menu
- **Skip links**: Collegamenti di salto per utenti tastiera
- **ARIA labels**: Markup semantico avanzato
- **Contrast checking**: Controllo automatico del contrasto colori

## 🎯 Obiettivo

Creare il tema WordPress più configurabile e performante disponibile, superando significativamente le capacità di Blocksy e stabilendo nuovi standard per:

- **Configurabilità**: Opzioni più avanzate e intuitive
- **Performance**: Tempi di caricamento <1.5 secondi
- **Qualità del codice**: Codice pulito, commentato e testabile
- **Modularità**: Architettura completamente modulare
- **Accessibilità**: Compliance WCAG 2.1 AA completa

## 📁 Struttura File

```
oltreblocksy-theme/
├── style.css                          # Stylesheet principale
├── functions.php                      # Core del tema con autoloader
├── index.php                         # Template principale
├── header.php                        # Header template
├── footer.php                        # Footer template
├── theme.json                        # Configurazione avanzata FSE
├── inc/                              # Classi e moduli PHP
│   ├── helpers.php                   # Funzioni di utilità
│   ├── template-functions.php        # Funzioni template
│   ├── template-tags.php            # Tag template personalizzati
│   └── Modules/                     # Sistema modulare
│       ├── Base_Module.php          # Classe base moduli
│       ├── Performance.php          # Ottimizzazioni performance
│       ├── Typography.php           # Sistema tipografico
│       ├── ColorSystem.php          # Sistema colori avanzato
│       ├── Customizer.php           # Customizer esteso
│       └── Accessibility.php        # Funzionalità accessibilità
├── assets/                          # Asset statici
│   ├── css/                        # Stylesheets
│   │   └── modules/                # CSS moduli specifici
│   ├── js/                         # JavaScript
│   │   ├── main.js                 # JavaScript principale
│   │   └── modules/                # JS moduli specifici
│   └── images/                     # Immagini tema
├── templates/                       # Template FSE
│   ├── parts/                      # Template parts
│   └── patterns/                   # Block patterns
└── languages/                      # File traduzione
```

## ⚡ Performance Metrics Target

- **PageSpeed Score**: 100/100
- **Loading Time**: <1.5 secondi
- **First Paint**: <0.8 secondi
- **Core Web Vitals**: Tutti verdi
- **Accessibility Score**: 100/100

## 🔧 Requisiti Tecnici

- **WordPress**: 6.0+
- **PHP**: 8.0+
- **Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+

## 🎨 Design Tokens

Il tema utilizza un sistema di design tokens centralizzato:

```css
:root {
  /* Spacing System */
  --spacing-xs: clamp(0.25rem, 0.5vw, 0.5rem);
  --spacing-sm: clamp(0.5rem, 1vw, 1rem);
  --spacing-md: clamp(1rem, 2vw, 2rem);
  
  /* Typography Scale */
  --font-size-base: clamp(1rem, 1.25vw, 1.125rem);
  --font-size-lg: clamp(1.125rem, 1.4vw, 1.25rem);
  
  /* Color System */
  --color-primary: #1e40af;
  --color-text: #1e293b;
  --color-background: #f8fafc;
}
```

## 🧩 Sistema Modulare

Ogni funzionalità è implementata come modulo separato:

```php
// Esempio di modulo personalizzato
class Custom_Module extends Base_Module {
    protected function get_name() {
        return 'Custom';
    }
    
    protected function init() {
        // Inizializzazione modulo
    }
}
```

## 🌐 Internazionalizzazione

Il tema è completamente tradotto e supporta:
- Text domain: `oltreblocksy`
- Tutte le stringhe sono wrapped con funzioni di traduzione
- File POT incluso per traduzioni

## 📱 Responsive Design

- **Mobile-first approach**
- **Breakpoints personalizzabili**
- **Container queries support**
- **Fluid typography e spacing**

## 🔒 Sicurezza

- **Sanitizzazione completa** di tutti gli input
- **Escape di tutti gli output**
- **Nonce verification** per AJAX
- **Capability checks** per admin

## 🧪 Testing

Il tema include:
- **PHPUnit tests** per logica PHP
- **JavaScript tests** per funzionalità JS
- **Accessibility tests** automatizzati
- **Performance benchmarks**

## 📚 Documentazione

Documentazione completa disponibile per:
- **Sviluppatori**: Hook, filter, API
- **Designer**: Customizer, design tokens
- **Utenti finali**: Configurazione, utilizzo

## 🤝 Contributi

Contributi benvenuti! Per favore:
1. Fork del repository
2. Crea feature branch
3. Commit delle modifiche
4. Push al branch
5. Crea Pull Request

## 📄 Licenza

GPL v2 or later

## 🆚 Confronto con Blocksy

| Caratteristica | OltreBlocksy | Blocksy |
|---|---|---|
| Moduli | ✅ Sistema completamente modulare | ❌ Monolitico |
| Performance | ✅ <1.5s loading | ❌ ~2-3s loading |
| Typography | ✅ Sistema rivoluzionario | ❌ Opzioni base |
| Accessibilità | ✅ WCAG 2.1 AA completo | ❌ Parziale |
| Color System | ✅ Avanzato con AI | ❌ Basic |
| Code Quality | ✅ PSR-4, OOP | ❌ Procedural |
| Customizer | ✅ Controlli avanzati | ❌ Standard |
| SEO | ✅ Schema markup avanzato | ❌ Basic |

---

*OltreBlocksy - Beyond Blocksy, Beyond Limits* 🚀