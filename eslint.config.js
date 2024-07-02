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
        project: "./tsconfig.json",
        extraFileExtensions: [".vue"],
        sourceType: "module",
      },
    },
  },
  eslintConfigPrettier,
  {
    rules: {
      "vue/match-component-import-name": "warn",
      "vue/match-component-file-name": [
        "error",
        {
          extensions: ["vue"],
          shouldMatchCase: true,
        },
      ],
      "vue/component-definition-name-casing": ["error", "PascalCase"],
      "vue/block-tag-newline": [
        "warn",
        {
          singleline: "always",
          multiline: "always",
          maxEmptyLines: 0,
        },
      ],
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
      "vue/require-default-prop": "off",
      "vue/multi-word-component-names": "off",
    },
  },
);
