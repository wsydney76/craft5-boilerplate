# Changelog

## 1.3.1 2025-05-18

* Use default Vite ports
* Added compression of assets to Vite build

## 1.3.0 2025-05-27

* Adjusted setup process to DDEV 1.24.5+: `Craft CMS settings now reside in .ddev/.env.web; .env is no longer managed by DDEV.`
* Always display an `Edit` link.
* Updated Craft CMS to 5.7.7.

## 1.2.0 2025-04-16

* Updated Craft CMS to 5.7.0.
* Updated section specific entry type names.
* Fixed a bug where a horizontal scrollbar appeared when setting an image width to `Full`.
* Fixed a styling issue with the `Quote` field type in the CP.
* Fixed a cosmetic issue where the seed controller replaced all dots in a generated title, not just the last one.

## 1.1.0 2025-04-01

* Added Copyright notice to images.
* Added `Quote` field type to matrix. *)
* Added layout options (width, full height)  to `Image` field type. *)
* Added `_cp/related-entries.twig` template to show related entries in the CP. Added as UI Element to `images` volume.
* Don't apply `2x` srcset to an image if it is not wide enough.
* Page navigation now shows first/last page links and `Page x of y`.
* Read me tweaks.
* Fix typos.
* Fixed a bug where the `seed` command would overwrite existing `alt` text for images.


*) Stolen from [craft5-tutorial](https://github.com/wsydney76/craft5-tutorial) as examples for applying different layout options to blocks.