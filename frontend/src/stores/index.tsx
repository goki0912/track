// store/postStore.ts
import { defineStore } from 'pinia';
import axios from 'axios';
import { Post, Track } from '@/types';

export const usePostStore = defineStore('post', {
    state: () => ({
        posts: [] as Post[],
    }),
    actions: {
        async fetchPosts() {
            const response = await axios.get<Post[]>('spotify/posts');
            this.posts = response.data;
        },
        async createPost(track: Track) {
            await axios.post('spotify/posts', track);
            await this.fetchPosts();
        },
    },
});
