# Craft 5 Boilerplate

This is the result of a web development workshop series with the goal to create a simple boilerplate from scratch.

![screenshot1](screenshot1.jpg)

While basic knowledge of HTML/CSS was assumed, the focus was on

* Principles of a CMS
* Creating a simple content model
* Setting up a multilingual site (english/german)
* Using a well-structured template system
* Creating dynamic markup
* Creating a responsive design
* Using a modern build pipeline for assets

Not all of these topics are covered in detail, but we tried to give a good overview of the possibilities.

Not in focus:

* Building a fancy design (yes, it is a bit boring...)
* Custom fonts
* Performance optimization
* Perfect image handling
* SEO
* Tailwind CSS customizations
* Setting up a local development environment/IDE, this was prepared in advance.

## Starting point

Started with a pure Craft CMS starter as provided in the craftcms/craft package with 
the following additions:

* Set system timezone to Europe/Berlin
* Use a single `.env.example` file for all environments
* Use environment specific config settings to config/general.php, dependent on CRAFT_ENVIRONMENT variable
* Added more config settings to config/general.php
* Added modules/_faux to enable autocompletion for some most frequently used variables in twig
* Added /web/cpresources, /node_modules, /config/license.key to .gitignore
* Added setup/install for automated installation under ddev, creates a user with user defined username/password.

## Boilerplate

* Added Vite powered asset pipeline with Tailwind CSS 4 and Alpine JS 3
* Setup 2 sites (english/german)
* Added local file systems and image volume
* Added basic sections and fields, including a simple matrix content builder
* Added basic templates
* Added basic image handling include
* Added mobile navigation via Alpine JS
* Added search
* Customized Control Panel (sources, preview targets.)
* Added german translations
* Added seed controller and example images to create dummy content
* Store all files that can be dynamically recreate in a single /web/dist directory (CP resources, Vite assets, image transforms)

We tried to add a lot of comments to the code, so you can easily follow along.

## DDEV Installation

* Clone repository
* `cd` into project directory
* Run `bash setup/install <projectname> <username> <password>`

### Seed content

* Add some images, min. 1024px wide
* Run `ddev craft seed` to create some dummy content
* If you want to start with your own content, update the `Settings/Singles` sections with your own content.
* If you don't want a multi-site setup, remove the second site in `Settings/Sites`.

## Non-DDEV Installation

Untested...

* Clone repository
* `cd` into project directory
* Prepare web server and database according to Craft CMS requirements
* Manually follow the steps in setup/install

## Plugins

* Uses [Vite plugin](https://plugins.craftcms.com/vite?craft5) by nystudio107 for building assets.
* No additional Craft CMS plugins are used, but you can add them as needed.
* Used the [fakerphp/faker package](https://github.com/fakerphp/faker) to create dummy content.

## Building assets

* Run `ddev npm run build` to build assets for production
* Run `ddev npm run dev` to build assets for development with instant reloading

### Used packages

* [@tailwindcss/vite Tailwind 4](https://tailwindcss.com/docs/installation/using-vite) for styling. This also installs Vite as a dependency.
* [@tailwindcss/typography](https://github.com/tailwindlabs/tailwindcss-typography) and [@tailwindcss/forms](https://github.com/tailwindlabs/tailwindcss-forms) plugins.
* [Alpine JS 3](https://alpinejs.dev/start-here) for interactivity. Only used for the mobile navigation.
* [@alpinejs/focus](https://alpinejs.dev/plugins/focus) for focus/keyboard handling inside the mobile navigation.

## Props

Thanks to Aylin, Karla, Lucy, Lori, Moni.