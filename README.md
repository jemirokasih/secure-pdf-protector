# Secure PDF Protector

Protect uploaded PDF files in WordPress so only logged-in users can access them.

## Features

- Protect PDF files inside `/wp-content/uploads/`
- Require WordPress login
- Block direct PDF access
- Compatible with Crocoblock / JetEngine
- Lightweight and simple
- No external dependencies

---

## Installation

1. Upload plugin to:

/wp-content/plugins/secure-pdf-protector/

2. Activate plugin from WordPress admin

3. Save permalink settings (don't change anything):
- Settings
- Permalinks
- Save Changes

---

## Apache Protection

Create `.htaccess` inside:

/wp-content/uploads/

Add:

```apache
<FilesMatch "\.(pdf)$">
Order Allow,Deny
Deny from all
</FilesMatch>
