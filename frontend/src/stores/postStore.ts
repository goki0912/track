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
      try {
        const response = await axios.get(`spotify/theme/${theme_id}/posts`);
        this.posts = response.data.posts;
        this.likedPosts = response.data.likedPosts;
      } catch (error) {
        console.error("Failed to fetch posts", error);
      }
    },
    async createPost(theme_id: number, track: Track) {
      try {
        await axios.post(`spotify/theme/${theme_id}/posts`, track);
        await this.fetchPosts(theme_id);
      } catch (error) {
        console.error("Failed to create a post", error);
      }
  },
});
