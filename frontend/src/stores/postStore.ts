import { defineStore } from "pinia";
import axios from "axios";
import { Post, Track } from "@/types";

export const usePostStore = defineStore("post", {
  state: () => ({
    posts: [] as Post[],
    likedPosts: [] as number[],
  }),
  actions: {
    async fetchPosts(theme_id: number) {
      const response = await axios.get(`spotify/theme/${theme_id}/posts`);
      this.posts = response.data.posts;

      this.likedPosts = response.data.likedPosts;
    },
    async createPost(theme_id: number, track: Track) {
      await axios.post(`spotify/theme/${theme_id}/posts`, track);
      await this.fetchPosts();
    },
  },
});
