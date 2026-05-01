# Smart Social Contact Panel

A lightweight, production-ready WordPress plugin that adds a floating social contact button to your site. Visitors click the button to open a clean popup panel with links to your chosen social/contact platforms.

## Features

- **20 supported platforms**: WhatsApp, Telegram, Messenger, Instagram, Facebook, X/Twitter, LinkedIn, TikTok, YouTube, Snapchat, Pinterest, Discord, Skype, Viber, Line, WeChat, Email, Phone Call, SMS, and Website.
- **Fully configurable admin UI** with 4 tabs: General, Platforms, Design, Advanced.
- **1, 2, or 3 column grid** layout with per-platform sort ordering.
- **Smooth animations**: fade, slide-up, zoom, or none.
- **Responsive** — works on desktop, tablet, and mobile. Optional full-width popup on mobile.
- **Accessible** — keyboard navigation, focus trap, ARIA labels, `prefers-reduced-motion` support.
- **Vanilla JavaScript** — no jQuery dependency.
- **No external requests** — all icons are inline SVG; no CDN.
- **Performance** — assets only load when the plugin is enabled; single DB option read per request.
- **Secure** — nonce verification, capability checks, full input sanitization and output escaping.
- **Export / Import** settings as JSON.

---

## Installation

### Via WordPress admin

1. Download or clone this repository.
2. Zip the `smart-social-contact-panel/` folder.
3. In your WordPress admin go to **Plugins → Add New → Upload Plugin**.
4. Upload the zip file and click **Install Now**.
5. Click **Activate Plugin**.

### Via FTP / cPanel

1. Upload the `smart-social-contact-panel/` folder to `/wp-content/plugins/`.
2. In your WordPress admin go to **Plugins** and activate **Smart Social Contact Panel**.

---

## Quick Start

1. After activation, click **Social Contact** in the left admin menu (or use the **Settings** link on the Plugins page).
2. On the **General** tab, toggle **Enable Plugin** to ON.
3. Switch to the **Platforms** tab, expand a platform (e.g., WhatsApp), toggle it **Enabled**, enter your phone number, and save.
4. Visit your website — the floating button will appear at the bottom-right (default).

---

## Admin Settings Reference

### Tab 1 — General

| Setting | Description |
|---|---|
| Enable Plugin | Master on/off switch. When off, no assets load on the frontend. |
| Button Position | Bottom-left or bottom-right of the viewport. |
| Button Text | Text shown next to the icon on the trigger button. |
| Side Text | Optional editable text shown beside the floating button. |
| Button Background Color | Hex color for the trigger button background. |
| Button Text Color | Hex color for button text and icon. |
| Popup Title | Heading shown at the top of the popup panel. |
| Popup Description | Optional rich text shown below the title. |
| Show Branding | Toggles the "Powered by Smart Social Contact Panel" footer line. |

### Tab 2 — Platforms

Each of the 20 platforms has:

| Field | Description |
|---|---|
| Enable/Disable | Toggle to show/hide this platform in the popup. |
| Display Name | Label shown on the platform card. |
| Value | Phone number, username, email, or URL depending on the platform. |
| Prefilled Message | WhatsApp-only optional message shown in WhatsApp before the visitor sends it. |
| Description | Optional subtitle on the card (e.g., "Chat with us"). |
| Sort Order | Lower numbers appear first. Use the ↑↓ arrows to reorder. |
| Open in New Tab | Whether clicking the card opens a new browser tab. |

WhatsApp also includes an optional **Prefilled Message** field. When set, visitors opening WhatsApp will see that message already written before they send it.

**Platform value formats:**

| Platform | Enter |
|---|---|
| WhatsApp / Viber / Phone / SMS | Phone number with country code: `+1234567890` |
| Telegram | `@username` or phone with country code |
| Instagram / Twitter / TikTok / Snapchat | `@username` or just `username` |
| Messenger / Pinterest / Skype / Line | Username |
| Facebook / LinkedIn / YouTube / Discord / WeChat / Website | Full URL or username/handle |
| Email | Email address: `you@example.com` |

### Tab 3 — Design

| Setting | Range | Description |
|---|---|---|
| Popup Width | 200–800 px | Desktop popup width. |
| Border Radius | 0–50 px | Popup corner rounding. |
| Box Shadow | On/Off | Drop shadow behind the popup. |
| Popup Background Color | Hex | Popup background. |
| Popup Text Color | Hex | Title and body text color. |
| Desktop Columns | 1 / 2 / 3 | Platform grid columns on desktop. |
| Mobile Columns | 1 / 2 | Grid columns on small screens. |
| Full-Width on Mobile | On/Off | Popup spans full screen width on mobile. |
| Card Background | Hex | Platform card background color. |
| Card Hover Color | Hex | Card background on mouse hover. |
| Icon Color Override | Hex | Uniform icon color. Check "Use platform brand colors" to use each platform's official color instead. |
| Card Border Radius | 0–50 px | Card corner rounding. |
| Open Animation | none / fade / slide-up / zoom | Popup entry animation. |

### Tab 4 — Advanced

- **Export Settings** — downloads all current settings as a JSON file.
- **Import Settings** — upload a previously exported JSON file and save.
- **Reset to Defaults** — resets all settings. A confirmation dialog prevents accidents.

---

## File Structure

```
smart-social-contact-panel/
├── smart-social-contact-panel.php   Main plugin file, bootstrap class
├── includes/
│   ├── helpers.php                  Static sanitization & URL-building utilities
│   ├── admin-settings.php           Admin menu, settings page, save handler
│   └── frontend-render.php          Frontend HTML output, inline SVG icons
├── assets/
│   ├── css/
│   │   ├── frontend.css             Frontend styles (BEM + CSS custom properties)
│   │   └── admin.css                Admin page styles
│   └── js/
│       ├── frontend.js              Vanilla JS: open/close, focus trap, ARIA
│       └── admin.js                 Admin JS: tabs, toggles, sort, export/import
└── README.md
```

---

## Developer Notes

### Option key

All settings are stored under a single WordPress option key `sscp_settings` (autoload=false). Retrieve with:

```php
$options = get_option( 'sscp_settings', [] );
$options = wp_parse_args( $options, SSCP_Settings::get_default_options() );
```

Or use the built-in accessor:

```php
$options = SSCP_Plugin::get_options();
```

### Adding a new platform

1. Add an entry to `SSCP_Settings::get_default_options()` under `platforms`.
2. Add the URL builder to `SSCP_Helpers::build_platform_url()`.
3. Add an SVG to `SSCP_Frontend::build_svg_map()`.
4. Add the brand color to `SSCP_Frontend::$brand_colors`.
5. Add value placeholder and label to `SSCP_Helpers::get_value_placeholder()` and `SSCP_Settings::get_value_field_label()`.

### Hooks available

```php
// Runs after SSCP_Plugin is instantiated
add_action( 'plugins_loaded', function() {
    // Your code here
}, 20 );
```

No public filters are defined in v1.0.0. File a feature request if you need one.

---

## Frequently Asked Questions

**Q: Will this slow my website down?**  
A: No. When enabled, two small files load (frontend.css ~5 KB, frontend.js ~2 KB). When disabled, nothing loads. There is one database read per page load.

**Q: Does it work with page builders (Elementor, Divi, Beaver Builder)?**  
A: Yes. The panel is output via `wp_footer`, which all major page builders respect.

**Q: Can I add custom CSS?**  
A: Yes. All elements use the `sscp-` class prefix. Add custom CSS via **Appearance → Customize → Additional CSS**.

**Q: My WhatsApp number isn't working. What format should I use?**  
A: Enter the phone number with country code, starting with `+`. Example: `+14155552671`. Do not include spaces or dashes.

**Q: The popup appears behind other elements on my site.**  
A: The panel uses `z-index: 99999`. If another element on your theme uses a higher z-index, add custom CSS: `.sscp-panel, .sscp-trigger, .sscp-overlay { z-index: 999999; }`.

---

## Changelog

### 1.0.0 — Initial Release
- 20 social/contact platforms with full URL building.
- 4-tab admin settings page.
- BEM CSS with CSS custom properties for runtime theming.
- Vanilla JS panel with focus trap and keyboard accessibility.
- JSON export/import.
- Responsive design with configurable mobile columns.

---

## License

GPL v2 or later. See [GNU General Public License](https://www.gnu.org/licenses/gpl-2.0.html).
