# wordpress-vite-config

ViteJS compatibility for Wordpress development

<img src="https://img.shields.io/badge/php-%5E8.0-blue">
<img src="https://img.shields.io/badge/wordpress-%3E%3D%206.0-blue">
<img src="https://img.shields.io/badge/node-%3E%3D%2016-brightgreen">

<br/>

## Project setup

### Setup your local develoment environment

<br/>

## Wordpress SETUP

### Setup [wp-config.php](./wp-config.php)

Paste the following line after the "define( 'WP_DEBUG', true );" constant in your file
```php
define( 'IS_VITE_DEVELOPMENT', false );
```

<br/>


## Setup development scripts

### Install dependencies

Éxécuter les commandes suivantes :

```bash
npm install && composer install
```

<br/>

### File **[vite.config.js](./vite.config.js)** setup

Change the value of `const url` with your local url project, in file [vite.config.js](./vite.config.js).

Change the value of `const themeName` with your theme name, in file [vite.config.js](./vite.config.js).

The list of entry files are defined in the [vite.config.js](./vite.config.js) file in the variable "entryFiles".

<br/>

### Tests the development scripts

Run the following command : 

```bash
npm run build
```


## Commands

### npm run watch

1. Change PHP constant "IS_VITE_DEVELOPMENT" to "true" in [wp-config.php](./wp-config.php) file.
2. Compile assets listed in the variable "entryFiles" in the [vite.config.js](./vite.config.js) file.
3. Refresh pages on JS change OR inject scss directly in the page

### npm run prod

1. Create a build folder in the theme with the assets fully compiled and fonts, pictures (if called in the assets)
2. Change PHP constant "IS_VITE_DEVELOPMENT" to "false" in [wp-config.php](./wp-config.php) file.

### npm run build

1. Run prettier for scss, lint for scss, prettier for js, lint for js and lint for php
2. Create a build folder in the theme with the assets fully compiled and fonts, pictures (if called in the assets)
3. Change PHP constant "IS_VITE_DEVELOPMENT" to "false" in [wp-config.php](./wp-config.php) file.
