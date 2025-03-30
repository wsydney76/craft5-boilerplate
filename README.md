# Craft 5 Basic Starter

This is a pure Craft CMS starter as provided in the craftcms/craft package with 
the following additions:


* Set system timezone to Europe/Berlin
* Use a single `.env.example` file for all environments
* Use environment specific config settings to config/general.php, dependent on CRAFT_ENVIRONMENT variable
* Added more config settings to config/general.php
* Added modules/_faux to enable autocompletion for some most frequently used variables in twig
* Added /web/cpresources, /node_modules, /config/license.key to .gitignore
* Added setup/install for automated installation under ddev, creates a user with user defined username/password.
* Prepared optional Tailwind installation
* Added scaffolding example page `templates/examples/page-simplecss.twig`
* Added scaffolding example page `templates/examples/page-tailwind.twig`
* Prepared for local plugin development and using the `extras` plugin as an example

## Boilerplate

* Store all files that can be dynamically recreate in a single /web/dist directory (CP resources, Vite assets, image transforms)
* Added Vite powered asset pipeline with Tailwind CSS 4 and Alpine JS 3
* Added local file systems and image volume
* Added basic sections and fields
* Added basic templates
* Added basic image handling macro
* Added mobile navigation via Alpine JS
* Added seed controller to create dummy content


## DDEV Installation

* Clone repository
* `cd` into project directory
* Run `bash setup/install <projectname> <username> <password>`

## Non-DDEV Installation

* Clone repository
* `cd` into project directory
* Prepare web server and database according to Craft CMS requirements
* Run `composer install`
* Run `./craft setup`
* Make sure `PRIMARY_SITE_URL` and `CRAFT_WEB_ROOT` are set correctly in `.env` file

