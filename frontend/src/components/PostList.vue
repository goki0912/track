<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Posts</h2>
    <div v-for="post in posts" :key="post.id" class="mb-4 p-4 border-b border-gray-200">
      <h3 class="text-xl font-semibold">{{ post.track.track_name }} by {{ post.track.artist_name }}</h3>
      <LikeButton
          :postId="post.id"
          :initialLikes="post.likes"
      />
      <img :src="post.track.album_image_url" alt="Album Art" class="w-20 h-20 mt-2">
      <p class="text-gray-500 mt-2">Posted by {{ post.user.name }}</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import { usePostStore } from '@/stores/postStore';
import { Post } from '@/types';
import LikeButton from "@/components/LikeButton.vue";

const postStore = usePostStore();
const posts = ref<Post[]>([]);

onMounted(async () => {
  await postStore.fetchPosts();
});

// postStoreのpostsが更新されたときに自動的に反映されるようにwatchを使う
watch(() => postStore.posts, (newPosts) => {
  posts.value = newPosts;
});
</script>

<style scoped>
</style>
