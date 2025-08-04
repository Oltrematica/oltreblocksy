# Guida all'Installazione - OltreBlocksy Theme

## 📋 Requisiti di Sistema

- **WordPress**: 6.0 o superiore
- **PHP**: 8.0 o superiore  
- **MySQL**: 5.7 o superiore
- **Memoria PHP**: Minimo 128MB (consigliato 256MB)
- **Browser**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+

## 🚀 Installazione

### Metodo 1: Upload diretto (Consigliato)

1. Scarica il file ZIP del tema
2. Accedi alla dashboard di WordPress
3. Vai su **Aspetto > Temi**
4. Clicca su **Aggiungi nuovo**
5. Clicca su **Carica tema**
6. Seleziona il file ZIP di OltreBlocksy
7. Clicca su **Installa ora**
8. Attiva il tema

### Metodo 2: Upload FTP

1. Estrai il file ZIP di OltreBlocksy
2. Carica la cartella `oltreblocksy-theme` in `/wp-content/themes/`
3. Accedi alla dashboard di WordPress  
4. Vai su **Aspetto > Temi**
5. Attiva OltreBlocksy

## ⚙️ Configurazione Iniziale

### 1. Configurazione di Base

Dopo l'attivazione, il tema è immediatamente funzionante con impostazioni predefinite ottimizzate. Per personalizzare:

1. Vai su **Aspetto > Personalizza**
2. Esplora le sezioni:
   - **Layout & Structure**: Configurazioni layout generale
   - **Styling & Colors**: Sistema colori e tipografia
   - **Performance & Optimization**: Ottimizzazioni performance

### 2. Impostazioni Consigliate

#### Performance
- ✅ Abilita "Performance Monitoring" (solo in sviluppo)
- ✅ Mantieni disabilitato "Force Load jQuery" (per migliori performance)

#### Typography  
- Scegli uno dei preset tipografici:
  - **Modern**: Per siti contemporanei e tech
  - **Elegant**: Per blog e portfolio
  - **Editorial**: Per siti di news e magazine
  - **Minimalist**: Per massime performance
  - **Creative**: Per portfolio creativi

#### Colori
- Scegli una palette colori:
  - **Professional**: Blu e grigi (default)
  - **Creative**: Viola e ciano
  - **Minimalist**: Nero e grigi
  - **Nature**: Verdi naturali

### 3. Menu di Navigazione

1. Vai su **Aspetto > Menu**
2. Crea un nuovo menu
3. Assegna alla posizione "Primary Menu"
4. Il tema supporta menu multi-livello con dropdown

### 4. Widget Footer

1. Vai su **Aspetto > Widget**
2. Aggiungi widget alle aree:
   - Footer 1, 2, 3, 4 (layout a 4 colonne)
   - Sidebar (se utilizzata)

## 🎨 Personalizzazioni Avanzate

### Design Tokens

Il tema utilizza CSS Custom Properties che puoi sovrascrivere:

```css
:root {
  --color-primary: #your-color;
  --font-heading: 'Your Font', sans-serif;
  --spacing-md: 2rem;
}
```

### Hook e Filter Personalizzati

```php
// Personalizza palette colori
add_filter('oltreblocksy_color_palettes', function($palettes) {
    $palettes['custom'] = array(
        'name' => 'Custom Palette',
        'colors' => array(
            'primary' => '#ff6b6b',
            'secondary' => '#4ecdc4',
            // ...
        ),
    );
    return $palettes;
});

// Personalizza preset tipografici
add_filter('oltreblocksy_typography_presets', function($presets) {
    $presets['custom'] = array(
        'name' => 'Custom Typography',
        'heading_font' => array(
            'family' => 'Custom Font',
            'google_font' => true,
        ),
        // ...
    );
    return $presets;
});
```

## 🔧 Moduli Opzionali

Tutti i moduli sono attivi per default. Per disabilitare:

```php
// Nel functions.php del child theme
add_filter('oltreblocksy_module_enabled', function($enabled, $module_name) {
    if ($module_name === 'HeaderBuilder') {
        return false; // Disabilita Header Builder
    }
    return $enabled;
}, 10, 2);
```

## 🎯 Ottimizzazioni Performance

### Configurazioni Server Consigliate

#### Apache (.htaccess)
```apache
# Cache statico
<IfModule mod_expires.c>
ExpiresActive on
ExpiresByType text/css "access plus 1 year"
ExpiresByType application/javascript "access plus 1 year"
ExpiresByType image/png "access plus 1 year"
</IfModule>

# Compressione Gzip
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE text/html
</IfModule>
```

#### Nginx
```nginx
# Cache statico
location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# Compressione
gzip on;
gzip_types text/css application/javascript text/html;
```

### Plugin Compatibili

- ✅ **WP Rocket**: Ottimizzazione cache
- ✅ **Autoptimize**: Minificazione CSS/JS  
- ✅ **Smush**: Ottimizzazione immagini
- ✅ **Yoast SEO**: SEO avanzato
- ✅ **WooCommerce**: E-commerce

### Plugin da Evitare

- ❌ Plugin di minificazione CSS/JS (il tema già ottimizza)
- ❌ Plugin di font Google (il tema gestisce i font)
- ❌ Plugin di critical CSS (il tema genera critical CSS)

## ⚡ Troubleshooting

### Problema: Tema non carica correttamente
**Soluzione**: Verifica i requisiti PHP e WordPress

### Problema: Font Google non caricano
**Soluzione**: Controlla connessione internet e firewall

### Problema: Customizer lento
**Soluzione**: Disabilita "Performance Monitoring" in produzione

### Problema: Layout rotto su mobile
**Soluzione**: Svuota cache browser e plugin

## 📞 Supporto

### Log di Debug

Abilita debug WordPress per vedere i log del tema:

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

I log del tema iniziano con `[OltreBlocksy]`.

### Performance Monitoring

In modalità debug, il tema logga automaticamente:
- Tempo di caricamento
- Uso memoria
- Query database
- Metrics Core Web Vitals

### Community e Documentazione

- 📚 **Documentazione completa**: [Link da aggiungere]
- 💬 **Forum di supporto**: [Link da aggiungere]
- 🐛 **Bug report**: [Link da aggiungere]
- 💡 **Feature request**: [Link da aggiungere]

---

**Congratulazioni! 🎉** 

Il tuo sito ora utilizza OltreBlocksy, il tema WordPress più avanzato e performante disponibile. Goditi le performance superiori e le infinite possibilità di personalizzazione!

*Per assistenza tecnica o domande avanzate, non esitare a contattare il supporto.*