import { defineStore } from "pinia";
import axios from "axios";
import { Post, Track } from "@/types";

export const usePostStore = defineStore("post", {
  state: () => ({
    posts: [] as Post[],
    likedPosts: [] as number[],
  }),
  actions: {
    async fetchPosts(themeId: number) {
      try {
        const response = await axios.get(`spotify/theme/${themeId}/posts`);
        this.posts = response.data.posts;
        this.likedPosts = response.data.likedPosts;
      } catch (error) {
        console.error("Failed to fetch posts", error);
      }
    },
    async createPost(themeId: number, track: Track) {
      try {
        await axios.post(`spotify/theme/${themeId}/posts`, track);
        await this.fetchPosts(themeId);
      } catch (error) {
        console.error("Failed to create a post", error);
      }
    },
  },
});
