import { defineStore } from "pinia";
import axios from "axios";
import { usePostStore } from "./postStore";

export const useLikeStore = defineStore("like", {
  state: () => ({
    loadingStates: {} as Record<number, boolean>,
  }),
  actions: {
    async toggleLike(postId: number, liked: boolean) {
      const postStore = usePostStore();

      // ロード中の確認、連打対策
      if (this.loadingStates[postId]) return;
      this.loadingStates[postId] = true;

      try {
        if (liked) {
          await axios.post(`/posts/${postId}/unlike`);
          postStore.likedPosts = postStore.likedPosts.filter(
              (id) => id !== postId,
          );
        } else {
          await axios.post(`/posts/${postId}/like`);
          postStore.likedPosts.push(postId);
        }

        const post = postStore.posts.find((post) => post.id === postId);
        if (post) {
          post.likes_count += liked ? -1 : 1;
        }
      } catch (error) {
        console.error(error);
      } finally {
        this.loadingStates[postId] = false;
      }
    }
  },
});
