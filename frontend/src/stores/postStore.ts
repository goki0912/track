import { defineStore } from "pinia";
import axios from "axios";
import { Post, Track } from "@/types";
import { useToast } from "vue-toast-notification";

export const usePostStore = defineStore("post", {
  state: () => ({
    posts: [] as Post[],
    likedPosts: [] as number[],
  }),
  actions: {
    async fetchPosts(themeId: number) {
      try {
        const response = await axios.get(`api/spotify/theme/${themeId}/posts`);
        this.posts = response.data.posts;
        this.likedPosts = response.data.likedPosts;
      } catch (error) {
        console.error("Failed to fetch posts", error);
      }
    },
    async createPost(themeId: number, track: Track) {
      const toast = useToast(); // トーストのインスタンスを取得
      try {
        await axios.post(`api/spotify/theme/${themeId}/posts`, track);
        await this.fetchPosts(themeId);
        toast.success("posted successfully!", {
          position: "top-right",
        });
      } catch (error) {
        console.error("Failed to create a post", error);
        toast.error("Failed to create a post", {
          position: "top-right",
        });
      }
    },
    async deletePost(themeId: number, postId: number) {
      const toast = useToast();
      try {
        await axios.delete(`api/spotify/posts/${postId}`);
        await this.fetchPosts(themeId);
        toast.success("Deleted successfully!", {
          position: "top-right",
        });
      } catch (error) {
        console.error("Failed to delete a post", error);
        toast.error("Failed to delete a post", {
          position: "top-right",
        });
      }
    },
  },
});
