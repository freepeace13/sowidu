// vite.config.mjs
import { defineConfig } from "file:///home/marc/market_dragon/app-web/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/marc/market_dragon/app-web/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///home/marc/market_dragon/app-web/node_modules/@vitejs/plugin-vue2/dist/index.mjs";
import path2 from "path";

// app/Modules/Offer/offer.vite.js
import path from "path";
var __vite_injected_original_dirname = "/home/marc/market_dragon/app-web/app/Modules/Offer";
var offer_vite_default = {
  input: [
    "app/Modules/Offer/resources/js/core.js",
    "app/Modules/Offer/resources/css/styles.css",
    "app/Modules/Offer/resources/css/pdf.css"
  ],
  alias: {
    "@Offer": path.resolve(__vite_injected_original_dirname, "./resources/js")
  }
};

// vite.config.mjs
var __vite_injected_original_dirname2 = "/home/marc/market_dragon/app-web";
var vite_config_default = defineConfig({
  resolve: {
    alias: {
      "@": path2.resolve(__vite_injected_original_dirname2, "./resources/js/"),
      "@components": path2.resolve(__vite_injected_original_dirname2, "./resources/js/Components"),
      "@Chatly": path2.resolve(__vite_injected_original_dirname2, "./modules/chatly/resources/js"),
      "@WorkLog": path2.resolve(__vite_injected_original_dirname2, "./modules/worklogs/resources/js"),
      // Modules aliases
      ...offer_vite_default?.alias || {}
    }
  },
  plugins: [
    laravel({
      input: [
        "resources/js/core.js",
        "resources/css/views/invoice-pdf.css",
        "resources/css/tailwind.css",
        "resources/css/fonts.css",
        "resources/css/vendor.css",
        "resources/css/styles.css",
        // Modules inputs
        ...offer_vite_default?.input || []
      ],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  server: {
    hmr: {
      host: "localhost"
    }
  },
  define: {
    "process.platform": JSON.stringify(process.platform)
    // 'process.env': {}, // TODO temporary fix for `process is not defined error on media page`
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcubWpzIiwgImFwcC9Nb2R1bGVzL09mZmVyL29mZmVyLnZpdGUuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS9tYXJjL21hcmtldF9kcmFnb24vYXBwLXdlYlwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL2hvbWUvbWFyYy9tYXJrZXRfZHJhZ29uL2FwcC13ZWIvdml0ZS5jb25maWcubWpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9ob21lL21hcmMvbWFya2V0X2RyYWdvbi9hcHAtd2ViL3ZpdGUuY29uZmlnLm1qc1wiO2ltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gJ3ZpdGUnXG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJ1xuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUyJ1xuaW1wb3J0IHBhdGggZnJvbSAncGF0aCdcbmltcG9ydCBvZmZlckNvbmZpZyBmcm9tICcuL2FwcC9Nb2R1bGVzL09mZmVyL29mZmVyLnZpdGUuanMnXG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcmVzb2x2ZToge1xuICAgICAgICBhbGlhczoge1xuICAgICAgICAgICAgJ0AnOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnLi9yZXNvdXJjZXMvanMvJyksXG4gICAgICAgICAgICAnQGNvbXBvbmVudHMnOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnLi9yZXNvdXJjZXMvanMvQ29tcG9uZW50cycpLFxuICAgICAgICAgICAgJ0BDaGF0bHknOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnLi9tb2R1bGVzL2NoYXRseS9yZXNvdXJjZXMvanMnKSxcbiAgICAgICAgICAgICdAV29ya0xvZyc6IHBhdGgucmVzb2x2ZShfX2Rpcm5hbWUsICcuL21vZHVsZXMvd29ya2xvZ3MvcmVzb3VyY2VzL2pzJyksXG5cbiAgICAgICAgICAgIC8vIE1vZHVsZXMgYWxpYXNlc1xuICAgICAgICAgICAgLi4uKG9mZmVyQ29uZmlnPy5hbGlhcyB8fCB7fSksXG4gICAgICAgIH0sXG4gICAgfSxcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFtcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2NvcmUuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvY3NzL3ZpZXdzL2ludm9pY2UtcGRmLmNzcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9jc3MvdGFpbHdpbmQuY3NzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2Nzcy9mb250cy5jc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvY3NzL3ZlbmRvci5jc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvY3NzL3N0eWxlcy5jc3MnLFxuXG4gICAgICAgICAgICAgICAgLy8gTW9kdWxlcyBpbnB1dHNcbiAgICAgICAgICAgICAgICAuLi4ob2ZmZXJDb25maWc/LmlucHV0IHx8IFtdKSxcbiAgICAgICAgICAgIF0sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgdnVlKHtcbiAgICAgICAgICAgIHRlbXBsYXRlOiB7XG4gICAgICAgICAgICAgICAgdHJhbnNmb3JtQXNzZXRVcmxzOiB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXG4gICAgICAgICAgICAgICAgICAgIGluY2x1ZGVBYnNvbHV0ZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pLFxuICAgIF0sXG4gICAgc2VydmVyOiB7XG4gICAgICAgIGhtcjoge1xuICAgICAgICAgICAgaG9zdDogJ2xvY2FsaG9zdCcsXG4gICAgICAgIH0sXG4gICAgfSxcbiAgICBkZWZpbmU6IHtcbiAgICAgICAgJ3Byb2Nlc3MucGxhdGZvcm0nOiBKU09OLnN0cmluZ2lmeShwcm9jZXNzLnBsYXRmb3JtKSxcbiAgICAgICAgLy8gJ3Byb2Nlc3MuZW52Jzoge30sIC8vIFRPRE8gdGVtcG9yYXJ5IGZpeCBmb3IgYHByb2Nlc3MgaXMgbm90IGRlZmluZWQgZXJyb3Igb24gbWVkaWEgcGFnZWBcbiAgICB9LFxufSlcbiIsICJjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZGlybmFtZSA9IFwiL2hvbWUvbWFyYy9tYXJrZXRfZHJhZ29uL2FwcC13ZWIvYXBwL01vZHVsZXMvT2ZmZXJcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi9ob21lL21hcmMvbWFya2V0X2RyYWdvbi9hcHAtd2ViL2FwcC9Nb2R1bGVzL09mZmVyL29mZmVyLnZpdGUuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL2hvbWUvbWFyYy9tYXJrZXRfZHJhZ29uL2FwcC13ZWIvYXBwL01vZHVsZXMvT2ZmZXIvb2ZmZXIudml0ZS5qc1wiO2ltcG9ydCBwYXRoIGZyb20gJ3BhdGgnXG5cbmV4cG9ydCBkZWZhdWx0IHtcbiAgICBpbnB1dDogW1xuICAgICAgICAnYXBwL01vZHVsZXMvT2ZmZXIvcmVzb3VyY2VzL2pzL2NvcmUuanMnLFxuICAgICAgICAnYXBwL01vZHVsZXMvT2ZmZXIvcmVzb3VyY2VzL2Nzcy9zdHlsZXMuY3NzJyxcbiAgICAgICAgJ2FwcC9Nb2R1bGVzL09mZmVyL3Jlc291cmNlcy9jc3MvcGRmLmNzcycsXG4gICAgXSxcblxuICAgIGFsaWFzOiB7XG4gICAgICAgICdAT2ZmZXInOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnLi9yZXNvdXJjZXMvanMnKSxcbiAgICB9LFxufVxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFvUixTQUFTLG9CQUFvQjtBQUNqVCxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU9BLFdBQVU7OztBQ0hxVCxPQUFPLFVBQVU7QUFBdlYsSUFBTSxtQ0FBbUM7QUFFekMsSUFBTyxxQkFBUTtBQUFBLEVBQ1gsT0FBTztBQUFBLElBQ0g7QUFBQSxJQUNBO0FBQUEsSUFDQTtBQUFBLEVBQ0o7QUFBQSxFQUVBLE9BQU87QUFBQSxJQUNILFVBQVUsS0FBSyxRQUFRLGtDQUFXLGdCQUFnQjtBQUFBLEVBQ3REO0FBQ0o7OztBRFpBLElBQU1DLG9DQUFtQztBQU16QyxJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUN4QixTQUFTO0FBQUEsSUFDTCxPQUFPO0FBQUEsTUFDSCxLQUFLQyxNQUFLLFFBQVFDLG1DQUFXLGlCQUFpQjtBQUFBLE1BQzlDLGVBQWVELE1BQUssUUFBUUMsbUNBQVcsMkJBQTJCO0FBQUEsTUFDbEUsV0FBV0QsTUFBSyxRQUFRQyxtQ0FBVywrQkFBK0I7QUFBQSxNQUNsRSxZQUFZRCxNQUFLLFFBQVFDLG1DQUFXLGlDQUFpQztBQUFBO0FBQUEsTUFHckUsR0FBSSxvQkFBYSxTQUFTLENBQUM7QUFBQSxJQUMvQjtBQUFBLEVBQ0o7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNIO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQTtBQUFBLFFBR0EsR0FBSSxvQkFBYSxTQUFTLENBQUM7QUFBQSxNQUMvQjtBQUFBLE1BQ0EsU0FBUztBQUFBLElBQ2IsQ0FBQztBQUFBLElBQ0QsSUFBSTtBQUFBLE1BQ0EsVUFBVTtBQUFBLFFBQ04sb0JBQW9CO0FBQUEsVUFDaEIsTUFBTTtBQUFBLFVBQ04saUJBQWlCO0FBQUEsUUFDckI7QUFBQSxNQUNKO0FBQUEsSUFDSixDQUFDO0FBQUEsRUFDTDtBQUFBLEVBQ0EsUUFBUTtBQUFBLElBQ0osS0FBSztBQUFBLE1BQ0QsTUFBTTtBQUFBLElBQ1Y7QUFBQSxFQUNKO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDSixvQkFBb0IsS0FBSyxVQUFVLFFBQVEsUUFBUTtBQUFBO0FBQUEsRUFFdkQ7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogWyJwYXRoIiwgIl9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lIiwgInBhdGgiLCAiX192aXRlX2luamVjdGVkX29yaWdpbmFsX2Rpcm5hbWUiXQp9Cg==
