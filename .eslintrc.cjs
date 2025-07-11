module.exports = {
    root: true,
    env: {
        browser: true,
        es2021: true,
        node: true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-recommended',
        'plugin:@typescript-eslint/recommended',
        'plugin:prettier/recommended',
    ],
    parser: 'vue-eslint-parser',
    parserOptions: {
        ecmaVersion: 'latest',
        parser: '@typescript-eslint/parser',
        sourceType: 'module',
    },
    plugins: ['vue', '@typescript-eslint', 'prettier'],
    rules: {
        'vue/first-attribute-linebreak': 'off',
        'vue/multi-word-component-names': 'off',
        'vue/no-v-html': 'off',
        'vue/require-default-prop': 'off',
        'vue/max-attributes-per-line': ['error', {
            singleline: {
                max: 5,
            },
            multiline: {
                max: 2,
            },
        }],
        'prettier/prettier': ['error', {
            singleQuote: true,
            semi: true,
            tabWidth: 2,
            trailingComma: 'es5',
            printWidth: 100,
            bracketSpacing: true,
            arrowParens: 'avoid',
        }],
        '@typescript-eslint/no-explicit-any': 'warn',
        '@typescript-eslint/no-unused-vars': ['error', {
            argsIgnorePattern: '^_',
            varsIgnorePattern: '^_',
        }],
    },
};
