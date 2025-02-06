import { defineStore } from "pinia";
import axios from "axios";
import { Theme } from "@/types";

export const useThemeStore = defineStore("theme", {
  state: () => ({
    themes: [] as Theme[],
  }),
  actions: {
    async fetchThemes() {
      try {
        const response = await axios.get("api/spotify/themes");
        this.themes = response.data.themes;
      } catch (error) {
        console.error("Failed to fetch themes:", error);
      }
    },
    // async createTheme(theme: Theme) {
    // await axios.post("spotify/themes", theme);
    // await this.fetchThemes();
    // },
  },
});
