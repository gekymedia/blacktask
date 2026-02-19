# Blacktask Launch Checklist

## Before you go live

### Web app (this project – D:\projects\blacktask)

- [ ] **.env for production**
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_KEY=` set (run `php artisan key:generate` if empty)
  - `APP_URL=` your real domain (e.g. `https://blacktask.gekymedia.com`)
- [ ] **Database** – migrations run, DB credentials correct in `.env`
- [ ] **Assets** – `npm run build` or `vite build` if you use compiled assets
- [ ] **HTTPS** – site served over HTTPS in production

### Mobile app (D:\projects\Flutter\blacktask)

- [ ] **Backend live** – API at `https://blacktask.gekymedia.com/api` is deployed and reachable
- [ ] **Release build**
  - Android: `flutter build apk --release` or `flutter build appbundle --release`
  - iOS: `flutter build ios --release` (then archive in Xcode)
- [ ] **Store listing** – use app name **Blacktask** and same icon as in the app

---

## Already in place ✓

- App name **Blacktask** and theme **#3B82F6** synced (web + mobile)
- Web: PWA manifest, favicons, theme-color
- Mobile: version 1.0.0+1, API URL, app name, Android/iOS launcher icons
