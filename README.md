# Craft 5 Boilerplate

This is the result of a web development workshop with the goal to create a simple boilerplate from scratch.

While basic knowledge of HTML/CSS was assumed, the focus was on

* Principles of a CMS
* Creating a simple content model
* Using a well-structured template system
* Creating dynamic markup
* Creating a responsive design

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
* Added basic sections and fields
* Added basic templates
* Added basic image handling include
* Added mobile navigation via Alpine JS
* Added search
* Added seed controller to create dummy content
* Store all files that can be dynamically recreate in a single /web/dist directory (CP resources, Vite assets, image transforms)

## DDEV Installation

* Clone repository
* `cd` into project directory
* Run `bash setup/install <projectname> <username> <password>`

### Seed content

* Add some images, min. 1024px wide
* Run `ddev craft seed` to create some dummy content

## Non-DDEV Installation

* Clone repository
* `cd` into project directory
* Prepare web server and database according to Craft CMS requirements
* Manually follow the steps in setup/install

