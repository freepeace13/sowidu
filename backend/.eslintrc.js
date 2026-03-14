module.exports = {
    extends: [
        'eslint:recommended',
        'plugin:vue/recommended',
        'prettier',
        // 'plugin:vuetify/base'
    ],
    parser: 'vue-eslint-parser',
    parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module',
    },
    plugins: ['vue'],
    env: {
        amd: true,
        browser: true,
        es6: true,
        node: true,
    },
    rules: {
        semi: ['off', 'always'],
        quotes: [
            'error',
            'single',
            { avoidEscape: true, allowTemplateLiterals: false },
        ],
        'comma-dangle': ['warn', 'always-multiline'],
        'vue/max-attributes-per-line': 'off',
        'vue/require-default-prop': 'off',
        'vue/singleline-html-element-content-newline': 'off',
        'vue/html-self-closing': [
            'warn',
            {
                html: {
                    void: 'always',
                    normal: 'always',
                    component: 'always',
                },
            },
        ],
        indent: [
            'error',
            4,
            {
                flatTernaryExpressions: false,
                offsetTernaryExpressions: false,
            },
        ],
        'vue/html-indent': [
            'error',
            4,
            {
                alignAttributesVertically: true,
                ignores: ['LogicalExpression'],
            },
        ],
        'vue/multi-word-component-names': 'off',
        'no-unused-vars': [
            'error',
            {
                ignoreRestSiblings: true,
                destructuredArrayIgnorePattern: '[A-Z]',
                caughtErrors: 'none',
            },
        ],
        'func-style': ['error', 'declaration', { allowArrowFunctions: true }],
        // 'vue/prefer-function-declaration': ['error'],
        // Allow function declarations in the <script> section of Vue files
        // 'vue/func-style': ['error', 'declaration'],
    },
}
