=== RASA - Social Contact Panel ===
Contributors: wplabtech
Tags: social, contact, whatsapp, floating button, popup
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight floating social contact button with a configurable popup panel for WhatsApp, social profiles, email, phone, SMS, and website links.

== Description ==

RASA - Social Contact Panel adds a floating contact button to your WordPress site. Visitors click the button to open a clean popup panel with your selected social and contact platforms.

The plugin is built for small businesses, agencies, portfolios, and service websites that need a simple way to expose multiple contact channels without loading external scripts or third-party assets.

= Features =

* 20 supported platforms: WhatsApp, Telegram, Messenger, Instagram, Facebook, X/Twitter, LinkedIn, TikTok, YouTube, Snapchat, Pinterest, Discord, Skype, Viber, Line, WeChat, Email, Phone Call, SMS, and Website.
* Configurable admin UI with General, Platforms, Design, and Advanced tabs.
* Editable floating button text and optional side text.
* Optional rich text popup description.
* WhatsApp prefilled message support.
* 1, 2, or 3 column popup grid layout.
* Per-platform labels, descriptions, sort order, and new-tab behavior.
* Responsive layout with optional full-width popup on mobile.
* Popup animations: fade, slide-up, zoom, or none.
* Keyboard navigation, focus handling, ARIA labels, and reduced-motion support.
* Vanilla JavaScript only. No jQuery dependency.
* No external asset requests. Icons are inline SVG.
* Export, import, and reset settings tools.

== Installation ==

1. Upload the `rasa-social-contact-panel` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the Plugins screen in WordPress.
3. Go to **Social Contact** in the WordPress admin menu.
4. Enable the plugin from the General tab.
5. Open the Platforms tab, enable the contact methods you want, enter their values, and save.

== Frequently Asked Questions ==

= Will this slow my website down? =

The frontend CSS and JavaScript load only when the plugin is enabled. The plugin does not load external scripts, remote fonts, or CDN assets.

= Does it work with page builders? =

Yes. The panel is rendered through `wp_footer`, which is supported by major WordPress themes and page builders.

= What WhatsApp number format should I use? =

Enter the number with the country code, for example `+14155552671`.

= Can I add a prefilled WhatsApp message? =

Yes. Expand WhatsApp in the Platforms tab and fill in the Prefilled Message field.

= Can I hide the powered-by text? =

Yes. Branding is disabled by default. If you want to show it, enable Show Branding on the General tab.

= Does the plugin track users? =

No. The plugin does not collect analytics, send tracking data, or contact external servers on its own.

== Changelog ==

= 1.0.2 =
* Updated plugin package naming and metadata.

= 1.0.1 =
* Updated plugin naming and package metadata for repository compliance.
* Switched frontend dynamic CSS to WordPress enqueue APIs.

= 1.0.0 =
* Initial release.
* Added 20 social and contact platforms.
* Added configurable floating button and popup panel.
* Added WhatsApp prefilled message support.
* Added responsive design settings.
* Added export/import settings tools.

== Upgrade Notice ==

= 1.0.1 =
Repository compliance update.

= 1.0.0 =
Initial release.
