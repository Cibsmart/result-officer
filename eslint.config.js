import pluginJs from "@eslint/js";
import pluginTs from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";

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
);
