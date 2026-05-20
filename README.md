# 🔴 FORKED_DATA — System Demonstracji Cyberzagrożeń

> ⚠️ **ZASTRZEŻENIE PRAWNE / DISCLAIMER**: Projekt powstał wyłącznie w celach **edukacyjnych i demonstracyjnych** w ramach zajęć kół naukowych. Wszelkie techniki i scenariusze opisane w tym repozytorium są prezentowane w kontrolowanym środowisku, za zgodą uczestników. Nielegalne użycie opisanych metod jest przestępstwem ściganych przez prawo.

---

## 📋 Opis projektu

**FORKED_DATA** to edukacyjna platforma demonstracyjna pokazująca, w jaki sposób cyberprzestępcy zbierają dane użytkowników — i jak się przed tym chronić.

Projekt symuluje środowisko zbierania danych na żywo: dane wpisywane przez użytkowników (w tym loginy, hasła, dane osobowe) oraz aktywność sieciowa są przechwytywane, analizowane i wyświetlane w czasie rzeczywistym w dashboardzie operatora. Całość służy jako materiał dydaktyczny do prezentacji zagrożeń bezpieczeństwa w sieci.

---

## 🎯 Cel demonstracji

Projekt ma na celu pokazanie:
- Jak łatwo jest stworzyć fałszywą stronę wyłudzającą dane
- Ile informacji można zebrać o użytkowniku bez jego wiedzy
- W jaki sposób **ponowne używanie nicków i haseł** dramatycznie zwiększa zasięg ataku
- Jak wycieki danych z przeszłości (HIBP) są aktywnie wykorzystywane przez hakerów
- Jak się chronić — polityka haseł, 2FA, menadżery haseł

---

## 🏗️ Podział ról (3 koła naukowe)

### 🛡️ AEGIS — Zbieranie Informacji
**Odpowiedzialny: Dominik**

Tworzenie metod i narzędzi wyciągania informacji od użytkowników:
- Klon strony logowania **Discord**
- Klon strony logowania **Facebook**
- Klon strony logowania **Steam**
- Strona **wizytówkowa** z QR kodami, linkami afiliacyjnymi i call-to-action
- Strona udająca **stronę uczelni** (formularz rejestracyjny)

Każda z emulacji jest zaprojektowana tak, aby wymusić jak najwięcej interakcji i zebrać maksimum danych: loginy, hasła, dane osobowe, adresy e-mail, numery telefonów.

---

### 🖧 NODE — Gromadzenie i Zarządzanie Danymi
**Odpowiedzialny: Eryk**

Infrastruktura fizyczna i sieciowa:
- Konfiguracja routera z **OpenWRT**
- Przygotowanie środowiska przechwytywania ruchu (laptop + dedykowany AP)
- Ustawienie **Reverse Proxy** do przekierowania ruchu
- Środowisko izolowane — własna sieć WiFi jako pułapka

---

### 📡 CONECT — Analiza i Wyświetlanie Danych
**Odpowiedzialny: Rafał (ten projekt)**

Dashboard na żywo oraz analiza danych:
- **Panel operatora** (ten projekt — Laravel + Filament)
- Wyświetlanie ruchu sieciowego i zebranych danych w czasie rzeczywistym
- Uruchamianie narzędzi OSINT na przechwyconych danych:
  - **Sherlock** — znajdowanie kont powiązanych z nickiem
  - **Have I Been Pwned** — sprawdzanie wycieków e-maili i haseł
- Demonstracja korelacji danych między różnymi platformami

---

## 🔍 Moduł CONECT — Szczegółowy Opis (ten projekt)

### Interaktywny Dashboard Operatora

Serwer Laravel z panelem **Filament Admin** służącym jako centrum dowodzenia:
- **Konsola terminala na żywo** — hacker-style interfejs do uruchamiania narzędzi
- **Wyświetlanie zebranych danych** w czasie rzeczywistym
- **Konfiguracja serwisów** do wyszukiwania

### Komenda `sherlock [nick]`

Symuluje działanie narzędzia [Sherlock](https://github.com/sherlock-project/sherlock) — skanuje popularne platformy w poszukiwaniu kont powiązanych z daną nazwą użytkownika.

**Dlaczego to ważne?**
> Jeśli ofiara używa tego samego nicku na wielu platformach (np. `j.kowalski` na Discord, Twitterze, GitHubie i Reddicie), przejęcie jednego konta pozwala hakerowi natychmiast zlokalizować pozostałe. Wystarczy jeden kompromitowany serwis, aby stracić prywatność na wszystkich.

```
guest@forkeddata:~$ sherlock j.kowalski
[*] Indexing target username: j.kowalski
[*] Scanning 6 active databases...
[+] FOUND: GitHub        => https://github.com/j.kowalski
[+] FOUND: Twitter / X   => https://x.com/j.kowalski
[-] NOT FOUND: Reddit
[+] FOUND: LinkedIn      => https://linkedin.com/in/j.kowalski
...
[*] Accounts located: 3 / 6
```

### Komenda `pwned [email]`

Symuluje działanie [Have I Been Pwned](https://haveibeenpwned.com/) — sprawdza, czy adres e-mail pojawił się w znanych bazach wyciekłych danych.

**Dlaczego to ważne?**

> Kiedy ofiara wpisuje e-mail i hasło na fałszywej stronie, operator natychmiast może sprawdzić:
> 1. Czy ten e-mail pojawił się w jakimkolwiek wycieku danych (Adobe 2013, LinkedIn 2016, itd.)
> 2. Jeśli tak — **jakie typy danych wyciekły** (hasła, adresy, dane karty)
> 3. Zestawić z właśnie przechwyconymi danymi

```
guest@forkeddata:~$ pwned jan.kowalski@gmail.com
[*] Querying 5 compromised datasets...
[!] PWNED: Adobe (October 2013)
    Leaked data: Email addresses, Passwords, Password hints
[!] PWNED: LinkedIn (May 2016)
    Leaked data: Email addresses, Passwords
[+] CLEAN: Canva
...
!!! WARNING !!! THIS EMAIL IS PWNED IN 2 BREACHES!
```

---

## 🔗 Łańcuch Ataku — Jak Dane Się Łączą

```
FAZA 1: Zbieranie               FAZA 2: Korelacja                FAZA 3: Eskalacja
─────────────────                ─────────────────────             ─────────────────
Użytkownik loguje się  ──────►  sherlock [nick]        ──────►   Inne konta
na klonie Discord               → 4 platformy znalezione          na wszystkich
                                                                    platformach
      │
      │  e-mail + hasło          pwned [email]          ──────►   "To samo hasło
      └──────────────  ──────►  → 2 wycieki z 2016r              było w wycieku Adobe!
                                                                    Prawdopodobnie
                                                                    używa go wszędzie."
```

**Scenariusz demonstracyjny:**
1. Uczestnik konferencji łączy się z "darmowym WiFi konferencyjnym" (router Eryka)
2. Wchodzi na klonową stronę Discord (Dominika) i wpisuje login + hasło
3. Na ekranie operatora (Rafał) pojawia się alert z danymi
4. Operator uruchamia `sherlock [nick]` → находит 3 inne konta na różnych platformach
5. Operator uruchamia `pwned [email]` → e-mail wyciekł w 2016 z LinkedIn
6. **Wniosek**: Ofiara prawdopodobnie używa tego samego hasła od 2016 roku na wszystkich serwisach

---

## 🧠 Dlaczego Ponowne Używanie Haseł Jest Niebezpieczne

| Scenariusz | Ryzyko |
|------------|--------|
| Ten sam nick na 10 serwisach | Jedno konto → mapa wszystkich profili |
| To samo hasło wszędzie | Jeden wyciek → dostęp do wszystkiego |
| E-mail znany z wycieku | Targeted phishing, credential stuffing |
| Nick = fragment imienia/nazwiska | Deanonimizacja, OSINT, doxing |
| Stare hasło z 2016 | Automatyczne ataki słownikowe działają natychmiast |

---

## 🛡️ Jak Się Chronić — Rekomendacje

### 🔑 Polityka Haseł
- **Unikalne hasło do każdego serwisu** — menadżer haseł (Bitwarden, 1Password, KeePass)
- **Minimum 16 znaków**, generowane losowo
- Nigdy nie używaj słów ze słownika, dat urodzin, imion
- Regularnie sprawdzaj wycieki: [haveibeenpwned.com](https://haveibeenpwned.com)

### 🔐 Uwierzytelnianie
- **2FA/MFA wszędzie gdzie możliwe** — klucz fizyczny (YubiKey), TOTP (Authy, Google Authenticator)
- Preferuj klucze sprzętowe nad SMS-em (SIM swapping!)
- Nawet jeśli hasło wycieknie — bez 2FA atakujący nie wejdzie

### 🌐 Bezpieczeństwo Sieciowe
- **Nigdy nie ufaj publicznym sieciom WiFi** — używaj VPN
- Sprawdzaj certyfikaty SSL (kłódka w przeglądarce)
- Uważaj na URL — `disc0rd.com` ≠ `discord.com`
- Używaj DNS-over-HTTPS (Cloudflare 1.1.1.1, NextDNS)

### 👤 Higiena Tożsamości Cyfrowej
- Różne nicki na różnych platformach
- Dedykowany e-mail alias do każdego serwisu (SimpleLogin, AnonAddy)
- Ogranicz dane osobowe na profilach publicznych
- Regularnie przeglądaj uprawnienia aplikacji

---

## 🛠️ Stack Technologiczny (NODE / CONECT)

| Warstwa | Technologia |
|---------|-------------|
| Backend | Laravel 11 (PHP 8.3) |
| Admin Panel | Filament v3 |
| Baza danych | MySQL 8.4 (Docker) |
| Cache | Redis Alpine (Docker) |
| Frontend | Blade + TailwindCSS + Vite |
| Mail | Mailpit (lokalne przechwytywanie) |
| Środowisko | Docker / Laravel Sail |

---

## 🚀 Uruchomienie

```bash
# Sklonuj repozytorium
git clone <repo-url>
cd forkeddata

# Skopiuj konfigurację
cp .env.example .env

# Uruchom kontenery Docker
./vendor/bin/sail up -d

# Zainstaluj zależności
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# Uruchom migracje i seeder
./vendor/bin/sail artisan migrate --seed

# Zbuduj assety
./vendor/bin/sail npm run build
```

### Dostęp

| URL | Opis | Login |
|-----|------|-------|
| `http://localhost` | Dashboard operatora (terminal) | — |
| `http://localhost/admin` | Panel Filament | `admin@example.com` / `password` |
| `http://localhost:8025` | Mailpit (e-mail interceptor) | — |

---

## 📊 Panel Administracyjny — Filament

Panel `/admin` zawiera:

### Sherlock — Symulator OSINT
- **Serwisy Sherlock** — Zarządzanie listą serwisów do przeszukiwania (GitHub, X, Reddit, Instagram, TikTok, LinkedIn) z wzorcami URL
- **Reguły Sherlock** — Definiowanie wyników wyszukiwania dla konkretnych nicków (FOUND / NOT FOUND)

### Have I Been Pwned — Symulator Wycieków
- **Bazy Wycieków HIBP** — Lista znanych baz danych wycieków (Adobe, LinkedIn, Canva, Zynga, Dropbox) z datą i opisem skompromitowanych danych
- **Reguły Wycieków HIBP** — Przypisywanie adresów e-mail do konkretnych wycieków (PWNED / CLEAN)

---

## 📁 Struktura Projektu

```
app/
├── Filament/Admin/Resources/
│   ├── SherlockServices/     # OSINT — serwisy do przeszukiwania
│   ├── SherlockRules/        # OSINT — reguły nick → serwis
│   ├── PwnedBreaches/        # HIBP — bazy wycieków
│   └── PwnedRules/           # HIBP — reguły email → wyciek
├── Models/
│   ├── SherlockService.php
│   ├── SherlockRule.php
│   ├── PwnedBreach.php
│   └── PwnedRule.php
database/
├── migrations/               # Schematy tabel
└── seeders/                  # Przykładowe dane
resources/views/
└── welcome.blade.php         # Dashboard operatora (terminal)
```

---

## 📚 Materiały i Linki

- [Sherlock Project](https://github.com/sherlock-project/sherlock) — Narzędzie OSINT do wyszukiwania kont
- [Have I Been Pwned](https://haveibeenpwned.com) — Baza danych wycieków
- [OWASP Top 10](https://owasp.org/www-project-top-ten/) — Najczęstsze podatności aplikacji webowych
- [Bitwarden](https://bitwarden.com) — Open-source menadżer haseł
- [HIBP API](https://haveibeenpwned.com/API/v3) — Oficjalne API do sprawdzania wycieków

---

*Projekt edukacyjny — koła naukowe | AEGIS · NODE · CONECT*
