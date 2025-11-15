# SIPA – Sistema Inteligente de Parqueaderos en Armenia

SIPA es una plataforma web diseñada para optimizar la administración, control y operación de parqueaderos en la ciudad de Armenia (Quindío).  
El sistema está desarrollado con Laravel, Blade y TailwindCSS, ofreciendo un estilo arquitectónico SOA y un patrón por capas, escalable y fácil de mantener.

---

## 🚀 Tecnologías principales

- **Laravel 11**
- **Blade Templates**
- **TailwindCSS v4**
- **Vite**
- **PHP 8.2+**

---

## 🧱 Requisitos

- PHP >= 8.2  
- Composer  
- Node.js >= 18  

---

## 🔧 Instalación

```bash
git clone https://github.com/sambuitr48/SIPA.git
cd SIPA
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run dev
php artisan serve