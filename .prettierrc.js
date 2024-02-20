// Prettier plugins are not working anymore.
// I have to use the parser option to specify the parser to use.
// https://github.com/prettier/prettier/issues/13276
module.exports = {
  plugins: [require.resolve("@prettier/plugin-php")],
  documenSelectors: ["**/*.php"],
  overrides: [
    {
      files: ["*.php"],
      options: {
        parseer: "php",
      },
    },
  ],
};
