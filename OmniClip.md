# Proyecto: OmniClip - Web Responsive para Descargar Videos y Multimedia

## 📱 Descripción General
OmniClip es una aplicación web responsive adaptada a celulares, tablets y computadoras de escritorio, capaz de analizar y descargar videos en alta definición (HD) y multimedia desde múltiples plataformas: Twitter (X), Instagram, TikTok, Facebook y YouTube.

---

## ⚙️ Funcionamiento de la Web

1. El usuario pega el enlace de Twitter (X), Instagram, TikTok, Facebook o YouTube.
2. El sitio detecta automáticamente la plataforma y envía la petición a la API backend (`api.php`).
3. La API procesa el enlace y obtiene la mejor calidad de stream disponible.
4. El usuario puede:
   - Descargar el archivo directamente a través del proxy seguro (`download.php`).
   - Previsualizar el video directamente en el reproductor HTML5 integrado.
   - Copiar el enlace directo de descarga al portapapeles.

---

## 🔌 Librerías y APIs

- **RapidAPI**
  - YouTube Data & Stream API
  - Twitter / X Media Downloader API
  - Social Media Video Downloader API (Instagram, TikTok, Facebook)

---

## 🧱 Stack de Desarrollo

- **Frontend**
  - HTML5 Semántico
  - Tailwind CSS (Diseño "Hyper-Luxe Prism", Glassmorphism y temas oscuros)
  - JavaScript (ES6+ y jQuery)

- **Backend**
  - PHP 8+
  - cURL con streaming continuo y protecciones avanzadas contra SSRF y Header Injection.

---

## 🎯 Estructura del Sitio (UX)

- Encabezado con branding moderno: **OmniClip**
- Barra de pestañas para filtrado o detección rápida por plataforma
- Selector de idioma multilenguaje (Inglés, Español, Portugués, Francés)
- Input inteligente con botones de Pegar desde portapapeles y Limpiar
- Tarjeta de resultado con thumbnail, título, badge de calidad HD y reproductor de video
- Botón de apoyo voluntario mediante PayPal
