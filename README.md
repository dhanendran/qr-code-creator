# QR Code Creator
Contributors: dhanendran
Tags: QR Code, Generator, QR Code Creator, QR Code Generator
Requires at least: 4.4
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: <a href="http://www.gnu.org/licenses/gpl-3.0.html">http://www.gnu.org/licenses/gpl-3.0.html</a>

Create QR codes in your WordPress admin — generated locally, with colors, a center logo, and PNG/SVG download.

== Description ==
QR Code Creator adds a simple generator under **Settings → QR Code Creator**. Enter any text or URL and get a QR code instantly.

As of 1.0.0, QR codes are generated **locally in your browser** using a bundled library. Your content is never sent to any third-party service — better for privacy and not dependent on an external API being online.

= Features =

* Generate QR codes from any text or URL.
* Choose the size and error-correction level.
* Customize foreground and background colors.
* Add a center logo / image from your Media Library.
* Download as PNG or SVG.
* 100% local — no external API calls, no data leaves your site.

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'
2. Search for `QR Code Creator`
3. Activate `QR Code Creator` from your Plugins page.

= From WordPress.org =

1. Download QR Code Creator.
2. Upload the 'qr-code-creator' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate QR Code Creator from your Plugins page.

== Changelog ==

= 1.0.0 =

* [Feature] QR codes are now generated locally in the browser using the bundled qr-code-styling library — the third-party goqr.me API dependency has been removed. Better privacy and no reliance on an external service.
* [Feature] Add a center logo / image from the Media Library.
* [Feature] Download generated QR codes as PNG or SVG.
* [Improvement] Refactored into an object-oriented structure with a dedicated admin class.
* [Improvement] Defaults to "High" error correction (recommended when using a center logo).

= 0.1.5 =

* Earlier releases generated QR codes via the third-party goqr.me API.
