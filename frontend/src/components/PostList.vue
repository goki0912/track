<!-- components/PostList.vue -->
<template>
  <div>
    <h2>Posts</h2>
    <div v-for="post in posts" :key="post.id" class="post">
      <h3>{{ post.track.track_name }} by {{ post.track.artist_name }}</h3>
      <img :src="post.track.album_image_url" alt="Album Art" class="w-20 h-20">
      <p>Posted by {{ post.user.name }}</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { usePostStore } from '@/stores';
import { Post } from '@/types';

const postStore = usePostStore();
const posts = ref<Post[]>([]);

onMounted(async () => {
  await postStore.fetchPosts();
  posts.value = postStore.posts;
});
</script>

<style scoped>
.post {
  border-bottom: 1px solid #ccc;
  padding: 16px 0;
}
</style>
