import { defineStore } from 'pinia';
import axios from 'axios';
import { usePostStore } from './postStore';

export const useLikeStore = defineStore('like', {
    state: () => ({}),
    actions: {
        async toggleLike(postId: number, liked: boolean) {
            const postStore = usePostStore();

            if (liked) {
                await axios.post(`/posts/${postId}/unlike`);
                postStore.likedPosts = postStore.likedPosts.filter(id => id !== postId);
            } else {
                await axios.post(`/posts/${postId}/like`);
                postStore.likedPosts.push(postId);
            }

            const post = postStore.posts.find(post => post.id === postId);
            if (post) {
                post.likes += liked ? -1 : 1;
            }
        },
    },
});
