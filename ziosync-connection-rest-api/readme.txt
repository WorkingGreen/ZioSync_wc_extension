=== ZioSync Connection REST API ===
Contributors: renszio
Tags: ziosync, reconcilliation, integration
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0.7
Requires PHP: 7.2
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin extends the WooCommerce REST API with additional endpoints specifically designed for seamless integration with ZioSync.

== Description ==

This plugin extends the WooCommerce REST API with additional endpoints specifically designed for seamless integration with ZioSync.

== Changelog ==

= 1.0.7 =
* Security: product lookup endpoints (search and SKU lookup) now enforce the same capability check as the WooCommerce product collection endpoint.
* Security: the customer meta lookup now requires both parameters, accepts only an allow-listed set of meta keys, and no longer returns sensitive user account fields.
* Security: hardened product media handling - the file type is now derived from the uploaded content and the filename is sanitised.

= 1.0.6 =
* Added order refunds endpoints.
