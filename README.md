# Cache Warmer for WordPress

## Plugin requirements:
* PHP 8.1+, might work with previous versions, but it's untested and I have no intention of supporting
* Yoast SEO plugin installed & configured to output a sitemap.xml file available at `yoursite.com/sitemap.xml`
* Should work on any WordPress version, but untested
* For development or monitoring the cache warmer, this plugin: `WP Crontrol` (https://wordpress.org/plugins/wp-crontrol/) is very useful, and can also be used to trigger the warmer manually
* Chromium installed on your server, needs to be an executable as stated here: https://github.com/chrome-php/chrome, if using Linux or a Linux Server, you can search for Chromium via Snap: `snap search chromium` and if one appears and is supported by your OS, install via `snap install chromium`

## How to make it work

1. Download the zip and upload the plugin
2. Activate the plugin
3. ???
4. Profit


## FAQs

Q: Can I set how often this runs?

A: No, it runs twice daily in 12 hour intervals

---

Q: Are you supporting this for anyone?

A: No, unless you pay me. Happy to answer questions as long as they're not annoying.

---

Q: Will this do absolutely everything some other cache warmer plugins would do?

A: No, it's basic AF

---

Good luck and happy cache warming.
