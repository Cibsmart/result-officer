import pluginJs from "@eslint/js";
import pluginTs from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";
import eslintConfigPrettier from "eslint-config-prettier";

export default pluginTs.config(
  pluginJs.configs.recommended,
  ...pluginTs.configs.recommended,
  ...pluginVue.configs["flat/recommended"],
  {
    plugins: {
      "typescript-eslint": pluginTs.plugin,
    },
    languageOptions: {
      parserOptions: {
        parser: pluginTs.parser,
        project: ["./tsconfig.json", "vite.config.js"],
        extraFileExtensions: [".vue"],
        sourceType: "module",
      },
    },
  },
  eslintConfigPrettier,
  {
    rules: {
      "@typescript-eslint/no-explicit-any": "off",

      "no-undef": "off",
      "no-unused-vars": "off",
      "no-console": ["warn"],

      "tailwindcss/no-custom-classname": "off",

      "vue/block-lang": ["error", { script: { lang: "ts" } }],
      "vue/block-tag-newline": [
        "warn",
        {
          singleline: "always",
          multiline: "always",
          maxEmptyLines: 0,
        },
      ],
      "vue/component-api-style": ["error", ["script-setup", "composition", "composition-vue2"]],
      "vue/component-definition-name-casing": ["error", "PascalCase"],
      "vue/component-name-in-template-casing": "error",
      "vue/component-tags-order": [
        "error",
        {
          order: ["script", "template", "style"],
        },
      ],
      "vue/define-emits-declaration": ["error", "type-based"],
      "vue/define-macros-order": [
        "error",
        {
          order: ["defineProps", "defineEmits"],
        },
      ],
      "vue/define-props-declaration": ["error", "type-based"],
      "vue/html-self-closing": [
        "error",
        {
          html: {
            void: "any",
            normal: "always",
            component: "always",
          },
          svg: "always",
          math: "always",
        },
      ],
      "vue/match-component-file-name": [
        "error",
        {
          extensions: ["vue"],
          shouldMatchCase: true,
        },
      ],
      "vue/match-component-import-name": "warn",
      "vue/multi-word-component-names": "off",
      "vue/no-ref-object-destructure": "error",
      "vue/no-undef-components": "error",
      "vue/no-unused-refs": "error",
      "vue/no-useless-v-bind": "error",
      "vue/no-v-html": "off",
      "vue/padding-line-between-tags": "warn",
      "vue/prefer-separate-static-class": "error",
      "vue/prefer-true-attribute-shorthand": "error",
      "vue/require-default-prop": "off",
    },
    ignores: ["*.d.ts"],
  },
);
