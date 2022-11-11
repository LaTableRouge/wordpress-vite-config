module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: ['standard'],
  parserOptions: {
    ecmaVersion: 12,
    sourceType: 'module',
  },
  globals: {
    jQuery: true,
    wp: true, // wordpress libraries
    wp_params: true // params when scripts are enqueued
  },
  plugins: [
    'unused-imports',
    'simple-import-sort'
  ],
  rules: {
    'object-shorthand': 0,
    'space-before-function-paren': 'off',
    'comma-dangle': ['error', 'only-multiline'],
    'generator-star-spacing': ['error', { before: false, after: true }],
    'unused-imports/no-unused-imports': 'warn',
    'simple-import-sort/imports': 'error',
    'simple-import-sort/exports': 'error',
    'no-console': 'warn',
    'operator-linebreak': ['error', 'before']
  }
}
