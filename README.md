# CHRISYSTEMATIXX e-Portfolio

A PHP e-portfolio running on XAMPP (Apache + PHP + MySQL).

## Live hosting
This project uses PHP and cannot run on GitHub Pages directly. Run locally with XAMPP or deploy to a PHP host (e.g., cPanel, PHP on Netlify/Render/Fly.io, etc.).

## Quick start (Local XAMPP)
1. Place the repo folder under your XAMPP htdocs, e.g. `C:\xampp\htdocs\EPORT`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Copy `CHRISYSTEMATIXX/inc/db.sample.php` to `CHRISYSTEMATIXX/inc/db.php` and update your MySQL credentials if needed.
4. Visit in your browser:
   - http://localhost/EPORT/CHRISYSTEMATIXX/

The app will create the database and tables automatically on first run (uses `CHRISYSTEMATIXX/sql/schema.sql`).

## Development
- Commit changes:
  ```bash
  git add -A
  git commit -m "Your message"
  git push
  ```
- Never commit secrets. `CHRISYSTEMATIXX/inc/db.php` is ignored by git.

## Repo
- GitHub: https://github.com/TopeTamad/EPORTFOLIO
