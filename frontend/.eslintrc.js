module.exports = {
  env: {
    browser: true,
    es2021: true,
    node: true,
  },
  extends: [
    "plugin:vue/essential",
    "standard",
  ],
  parserOptions: {
    ecmaVersion: 12,
    parser: "@typescript-eslint/parser",
    sourceType: "module",
  },
  plugins: [
    "vue",
    "@typescript-eslint",
  ],
  // prettierと合わせてダブルクオート,セミコロンあり、配列の最後にカンマあり、関数名の前にスペースなしで統一
  rules: {
    quotes: ["error", "double"],
    semi: ["error", "always"],
    "comma-dangle": ["error", "always-multiline"],
    "space-before-function-paren": ["error", {
      anonymous: "never", // 無名関数のスペースをなくす
      named: "never", // 名前付き関数のスペースをなくす
      asyncArrow: "always", // asyncアロー関数の後にスペースを入れる
    },
    ],
  },
};
