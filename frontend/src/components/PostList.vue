<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Posts</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="post in posts" :key="post.id" class="mb-4 p-4 border-b border-gray-200 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-semibold">{{ post.track.track_name }} by {{ post.track.artist_name }}</h3>
        <LikeButton
            :postId="post.id"
            :initialLikes="post.likes"
        />
        <img :src="post.track.album_image_url" alt="Album Art" class="w-20 h-20 mt-2">
        <button @click="playTrack(post.track.uri)" class="mt-2 bg-blue-500 text-white py-1 px-3 rounded">
          Play
        </button>
        <p class="text-gray-500 mt-2">Posted by {{ post.user.name }}</p>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import { usePostStore } from '@/stores/postStore';
import { Post } from '@/types';
import LikeButton from "@/components/LikeButton.vue";
import axios from "axios";

const postStore = usePostStore();
const posts = ref<Post[]>([]);

onMounted(async () => {
  await postStore.fetchPosts();
});

// postStoreのpostsが更新されたときに自動的に反映されるようにwatchを使う
watch(() => postStore.posts, (newPosts) => {
  posts.value = newPosts;
});

const playTrack = async (trackUri: string) => {
  try {
    const accessToken = sessionStorage.getItem('spotify_access_token');
    if (!accessToken) {
      console.error('Access token not found');
      return;
    }
    console.log(accessToken);
    console.log("aaaaaaa", trackUri);
    const response = await axios.post('spotify/play-track', {
      uri: trackUri
    },
    {
      headers: {
        spotifyAuthorization: `Bearer ${accessToken}`
      }
    }
    );
    if (response.status === 200) {
      console.log('Track is playing');
    } else {
      console.log('Failed to play track', response.status);
    }
  } catch (error) {
    console.error('Error playing track', error);
  }
};
</script>

<style scoped>
</style>
