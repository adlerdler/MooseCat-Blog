// vite.config.js
import { defineConfig } from "file:///I:/Code%20editing/bolg_laravel_adlerian/laravel-vue-app/node_modules/vite/dist/node/index.js";
import laravel from "file:///I:/Code%20editing/bolg_laravel_adlerian/laravel-vue-app/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///I:/Code%20editing/bolg_laravel_adlerian/laravel-vue-app/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import path from "path";
import prismjs from "file:///I:/Code%20editing/bolg_laravel_adlerian/laravel-vue-app/node_modules/vite-plugin-prismjs/dist/index.js";
var __vite_injected_original_dirname = "I:\\Code editing\\bolg_laravel_adlerian\\laravel-vue-app";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    prismjs({
      languages: ["javascript", "typescript", "python", "java", "css", "scss", "markup", "json", "yaml", "bash", "jsx", "tsx", "sql", "rust", "go", "php"],
      theme: "okaidia",
      css: true
    })
  ],
  resolve: {
    alias: {
      "@": path.resolve(__vite_injected_original_dirname, "./resources/js"),
      "vue": "vue/dist/vue.esm-bundler.js"
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJJOlxcXFxDb2RlIGVkaXRpbmdcXFxcYm9sZ19sYXJhdmVsX2FkbGVyaWFuXFxcXGxhcmF2ZWwtdnVlLWFwcFwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiSTpcXFxcQ29kZSBlZGl0aW5nXFxcXGJvbGdfbGFyYXZlbF9hZGxlcmlhblxcXFxsYXJhdmVsLXZ1ZS1hcHBcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0k6L0NvZGUlMjBlZGl0aW5nL2JvbGdfbGFyYXZlbF9hZGxlcmlhbi9sYXJhdmVsLXZ1ZS1hcHAvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnO1xuaW1wb3J0IHBhdGggZnJvbSAncGF0aCc7XG5pbXBvcnQgcHJpc21qcyBmcm9tICd2aXRlLXBsdWdpbi1wcmlzbWpzJztcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFsncmVzb3VyY2VzL2Nzcy9hcHAuY3NzJywgJ3Jlc291cmNlcy9qcy9hcHAuanMnXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuICAgICAgICB2dWUoe1xuICAgICAgICAgICAgdGVtcGxhdGU6IHtcbiAgICAgICAgICAgICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgICAgICAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgICAgICAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSksXG4gICAgICAgIHByaXNtanMoe1xuICAgICAgICAgICAgbGFuZ3VhZ2VzOiBbJ2phdmFzY3JpcHQnLCAndHlwZXNjcmlwdCcsICdweXRob24nLCAnamF2YScsICdjc3MnLCAnc2NzcycsICdtYXJrdXAnLCAnanNvbicsICd5YW1sJywgJ2Jhc2gnLCAnanN4JywgJ3RzeCcsICdzcWwnLCAncnVzdCcsICdnbycsICdwaHAnXSxcbiAgICAgICAgICAgIHRoZW1lOiAnb2thaWRpYScsXG4gICAgICAgICAgICBjc3M6IHRydWUsXG4gICAgICAgIH0pLFxuICAgIF0sXG4gICAgcmVzb2x2ZToge1xuICAgICAgICBhbGlhczoge1xuICAgICAgICAgICAgJ0AnOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnLi9yZXNvdXJjZXMvanMnKSxcbiAgICAgICAgICAgICd2dWUnOiAndnVlL2Rpc3QvdnVlLmVzbS1idW5kbGVyLmpzJyxcbiAgICAgICAgfSxcbiAgICB9LFxufSk7XG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQTJWLFNBQVMsb0JBQW9CO0FBQ3hYLE9BQU8sYUFBYTtBQUNwQixPQUFPLFNBQVM7QUFDaEIsT0FBTyxVQUFVO0FBQ2pCLE9BQU8sYUFBYTtBQUpwQixJQUFNLG1DQUFtQztBQU16QyxJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUV4QixTQUFTO0FBQUEsSUFDTCxRQUFRO0FBQUEsTUFDSixPQUFPLENBQUMseUJBQXlCLHFCQUFxQjtBQUFBLE1BQ3RELFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNBLFVBQVU7QUFBQSxRQUNOLG9CQUFvQjtBQUFBLFVBQ2hCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ3JCO0FBQUEsTUFDSjtBQUFBLElBQ0osQ0FBQztBQUFBLElBQ0QsUUFBUTtBQUFBLE1BQ0osV0FBVyxDQUFDLGNBQWMsY0FBYyxVQUFVLFFBQVEsT0FBTyxRQUFRLFVBQVUsUUFBUSxRQUFRLFFBQVEsT0FBTyxPQUFPLE9BQU8sUUFBUSxNQUFNLEtBQUs7QUFBQSxNQUNuSixPQUFPO0FBQUEsTUFDUCxLQUFLO0FBQUEsSUFDVCxDQUFDO0FBQUEsRUFDTDtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ0wsT0FBTztBQUFBLE1BQ0gsS0FBSyxLQUFLLFFBQVEsa0NBQVcsZ0JBQWdCO0FBQUEsTUFDN0MsT0FBTztBQUFBLElBQ1g7QUFBQSxFQUNKO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
