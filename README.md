# wordpress-vite-config

ViteJS compatibility for Wordpress development as a theme

<img src="https://img.shields.io/badge/php-%5E8.0-blue">
<img src="https://img.shields.io/badge/wordpress-%3E%3D%206.5-blue">
<img src="https://img.shields.io/badge/node-%3E%3D%2022-brightgreen">

<br/>

Inspired by https://github.com/blonestar/wp-theme-vite-tailwind and https://github.com/andrefelipe/vite-php-setup

<br/>

## Project setup

### Setup your local develoment environment

<br/>

## Install dependencies

Do the following commands:

```bash
npm install && composer install
```

<br/>

## Setup files

### File **[vite.config.mjs](./vite.config.mjs)** setup

Change the value of `const themeName` with your theme name, in file [vite.config.mjs](./vite.config.mjs).

The list of entry files are defined in the [vite.config.mjs](./vite.config.mjs) file in the variable "entryFiles".

### File **[functions.php](./functions.php)** setup

Require the [vite.php](./inc/vite.php) file.

You can see there some use cases in the [functions.php](./functions.php) file on how to enqueue assets.

<br/>

### Tests the development scripts

Run the following command: 

```bash
npm run build
```


## Commands

### npm run watch

1. Change PHP constant "IS_VITE_DEVELOPMENT" to "true" in the [theme functions.php](./functions.php) file.
2. Compile assets listed in the variable "entryFiles" in the [vite.config.mjs](./vite.config.mjs) file.
3. Refresh pages on JS change OR inject scss directly in the page.

### npm run build

1. Create a build folder in the theme with the assets fully compiled and fonts, pictures (if called in the assets).
2. Change PHP constant "IS_VITE_DEVELOPMENT" to "false" in the [theme functions.php](./functions.php) file.

### npm run beautify:all (optional)

1. Prettify and lint all files listed in the [package.json](./package.json) commands.

