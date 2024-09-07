<template>
  <div class="flex items-center">
    <button
      @click="toggleLike"
      :class="liked ? 'text-red-500' : 'text-gray-500'"
      class="focus:outline-none"
    >
      <svg v-if="liked" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path
          d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
        />
      </svg>
      <svg
        v-else
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path
          d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
        />
      </svg>
    </button>
    <span class="ml-2 text-gray-600">{{ likes }}</span>
  </div>
</template>

<script lang="ts" setup>
import { ref, defineProps } from "vue";
import { useLikeStore } from "@/stores/likeStore";
import { usePostStore } from "@/stores/postStore";

const props = defineProps({
  postId: {
    type: Number,
    required: true,
  },
  initialLikes: {
    type: Number,
    required: true,
  },
});

const likeStore = useLikeStore();
const postStore = usePostStore();
const likes = ref(props.initialLikes);
const liked = ref(postStore.likedPosts.includes(props.postId));

const toggleLike = async () => {
  await likeStore.toggleLike(props.postId, liked.value);
  liked.value = !liked.value;
  likes.value += liked.value ? 1 : -1;
};
</script>
