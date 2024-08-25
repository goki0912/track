import { defineStore } from "pinia";
import axios from "axios";
import { Post, Track } from "@/types";

export const usePostStore = defineStore("post", {
  state: () => ({
    posts: [] as Post[],
    likedPosts: [] as number[],
  }),
  actions: {
    async fetchPosts() {
      const response = await axios.get("spotify/posts");
      this.posts = response.data.posts;
      this.likedPosts = response.data.likedPosts;
    },
    async createPost(track: Track) {
      await axios.post("spotify/posts", track);
      await this.fetchPosts();
    },
  },
});
