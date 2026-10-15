# Scoped Search & Replace website: owner guide (Hinglish)

Ye WordPress website tumhare free plugin **Scoped Search & Replace** ke liye hai (plugin ka code:
`Downloads\wordpress-search-replace-tool`). Abhi ye sirf tumhare computer par chalti hai.
Bulk Uploader aur Reorder Desk websites jaisi hi bani hai, to wahan jo seekha woh yahan bhi kaam aayega.

## 1. Site kholna

1. XAMPP Control Panel kholo, **Apache** aur **MySQL** dono Start karo.
2. Browser mein: **http://localhost:8091** (website)
3. Admin panel: **http://localhost:8091/wp-admin/**
   - User aur password: `search-replace-website/local-login.txt` mein hain. Ye file kisi ko mat bhejna.

Port 8090 par pehle se ek "portfolio" site hai, isliye ye 8091 par hai. Local site pehli baar thodi slow khulti hai.

## 2. Website mein kya hai

| Page | Address |
|---|---|
| Home | `/` |
| Features | `/features/` |
| How it works (asli example, 5 steps) | `/how-it-works/` |
| Download (ZIP + GitHub, install steps) | `/download/` |
| Documentation (11 sections + troubleshooting) | `/docs/` |
| Changelog (1.1.0, 1.0.0) | `/changelog/` |
| Contact (form) | `/contact/` |
| Privacy policy | `/privacy-policy/` |
| Blog (4 guides) | `/blog/` |

- Saare screenshots **asli plugin** ke hain: ek alag demo WordPress (`http://localhost/srdemo`, ek coffee shop ka
  sample content) par plugin chala kar liye gaye. Wahan login: user `demo`, password `demo-local-only` (sirf local).
- Website par likhi har baat plugin ke code se check ki gayi hai (kya karta hai, kya nahi karta).
- Plugin khud bhi is website par install hai (wp-admin > Search & Replace), try kar sakte ho.

## 3. Download button kaise kaam karta hai

- "Download version 1.1.0 (ZIP)" button website ke andar rakhi ZIP deta hai:
  `plugin/scoped-sr-site/download/scoped-search-replace.zip` (tumhari `scoped-search-replace.zip` ki copy).
- **Naya version aaye to:** nayi ZIP isi naam se us folder mein daal do, aur Settings > **Scoped Search & Replace
  site** > "Current plugin version" badal do. Changelog page mein naya version likh do (ya Claude se karwa lo).
- "Source code on GitHub" button GitHub repo par jaata hai. **Repo public hai**, to log code dekh sakte hain.

## 4. WordPress.org par listing ke din (sirf 1 setting)

Settings > Scoped Search & Replace site > **WordPress.org plugin page** mein link daalo
(`https://wordpress.org/plugins/scoped-search-replace/`) > Save. Header, home aur baaki pages ke saare
"Download free" buttons apne aap **"Get it on WordPress.org"** ban jayenge. Download page ka ZIP button waisa hi
rahega. Phir Download page ka FAQ "Will it be in the WordPress.org plugin directory?" badal dena.

Note: maine 15 Oct 2026 ko check kiya, WordPress.org par `scoped-search-replace` naam ka plugin abhi nahi hai
(naam free hai). Kya tumne plugin review ke liye submit kiya hai? Website par sirf itna likha hai ki plugin
"prepared for the directory" hai.

## 5. Text ya page badalna, naya blog post

- **Koi page:** wp-admin > Pages > page par click > text badlo > **Save**.
- **Header / footer:** wp-admin > Appearance > Editor > Patterns > Template parts.
- **Naya section:** page mein `+` > Patterns > "Scoped Search & Replace" (CTA band, feature cards, FAQ,
  screenshot ke saath text).
- **Blog post:** Posts > Add New: title, text, Category (Guides), Featured image (1600x900), Excerpt, Yoast meta
  description > Publish.

Achhe blog topics (log ye search karte hain): "WordPress change URL in database", "WordPress find and replace text in
all posts", "Elementor replace URL", "WordPress update links after migration", "WordPress staging to live URL",
"remove old domain links WordPress".

## 6. Contact messages

- wp-admin > **Messages**: saare messages; "Download CSV" se Excel mein.
- Local computer se email nahi jaati, par message hamesha Messages mein save hota hai.
- Settings > Scoped Search & Replace site: support email aur "messages kis email par aayein" bharo.

## 7. Website live karna (domain + hosting)

1. **Domain lo.** `scopedsearchreplace.com` jaisa naam; khareedte waqt dekhna free hai ya nahi.
2. **Hosting:** PHP 8.2+, MySQL, free SSL. Ek hi hosting par teeno websites rakh sakte ho. Site domain ke root par
   honi chahiye (folder mein nahi).
3. **Shift:** local wp-admin mein **All-in-One WP Migration** > Export > File; hosting par naya WordPress + wahi
   plugin > Import. (Ya Claude ko hosting access do.)
4. Live site par: Settings > Permalinks > Save (ek baar). Settings > Scoped Search & Replace site mein emails bharo.
5. **WP Mail SMTP** plugin, taaki messages email par aayein. Google Search Console mein sitemap:
   `https://<domain>/sitemap_index.xml`.
6. Plugin ke `readme.txt` / plugin header mein "Plugin URI" abhi GitHub hai; chaho to website ka link daal sakte ho.

## 8. Abhi tumhe check karna hai

- Contact page par "1 working day mein reply" ka vaada theek hai?
- Privacy policy: contact messages "baat khatam hone ke 12 mahine mein delete" theek hai? Company / legal naam
  daalna hai to batao.
- Plugin ke `readme.txt` ka FAQ kehta hai "menus search nahi hote", par code mein "All content types" chunne par menu
  items (aur revisions) bhi scope mein aa jaate hain. Website par code ke hisaab se sahi likha hai; readme theek
  karna ho to Claude se kaho.
