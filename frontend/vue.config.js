module.exports = {
  transpileDependencies: true,
  chainWebpack: config => {
    config.module
      .rule("vue")
      .use("vue-loader")
      .loader("vue-loader")
      .tap(options => {
        // `options`に必要な設定を追加
        return options;
      });
  },
};
