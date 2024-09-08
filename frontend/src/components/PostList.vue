<template>
  <div @click="handleClickOutside">
    <h2 class="text-2xl font-bold mb-4">POSTS</h2>
    <p>おだい</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="post in posts"
        :key="post.id"
        class="flex items-center p-4 border-b border-gray-200 bg-white rounded-lg shadow-md mx-2"
        :class="
          currentUser && currentUser.id === post.user.id ? 'bg-emerald-100' : ''
        "
      >
        <!-- アルバム画像を曲名の前に表示 -->
        <img
          :src="post.track.album_image_url"
          alt="Album Art"
          class="w-12 h-12 mr-4 rounded"
        />
        <div class="flex-1">
          <!-- 曲名とアーティスト名 -->
          <h3 class="text-base font-semibold">
            {{ post.track.track_name }} <br />
            <span class="text-sm text-gray-600">{{
              post.track.artist_name
            }}</span>
          </h3>
        </div>

        <!-- Likeボタン -->
        <LikeButton :postId="post.id" :initialLikes="post.likes" />

        <!-- ケバブボタン -->
        <div class="relative">
          <button
            @click="toggleMenu(post.id)"
            class="ml-2"
            :ref="(el) => (menuButtonRefs[post.id] = el)"
          >
            <!-- ケバブボタン（3つのドット） -->
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6v.01M12 12v.01M12 18v.01"
              ></path>
            </svg>
          </button>

          <!-- メニュー (Play, Deleteボタン) -->
          <div
            v-if="isMenuOpen(post.id)"
            class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
            :ref="(el) => (menuRefs[post.id] = el)"
          >
            <button
              @click="playTrack(post.track.uri)"
              class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-gray-100"
            >
              Play
            </button>
            <button
              v-if="currentUser && currentUser.id === post.user.id"
              @click="deletePost(post.id)"
              class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from "vue";
import { usePostStore } from "@/stores/postStore";
import { Post } from "@/types";
import LikeButton from "@/components/LikeButton.vue";
import axios from "axios";

const postStore = usePostStore();
const posts = ref<Post[]>([]);
const currentUser = ref<{ id: number; name: string } | null>(null);

onMounted(async () => {
  await postStore.fetchPosts();
  await fetchCurrentUser();
});

// postStoreのpostsが更新されたときに自動的に反映されるようにwatchを使う
watch(
  () => postStore.posts,
  (newPosts) => {
    posts.value = newPosts;
  },
);

const playTrack = async (trackUri: string) => {
  try {
    const accessToken = sessionStorage.getItem("spotify_access_token");
    if (!accessToken) {
      console.error("Access token not found");
      return;
    }
    const device = await axios.get("spotify/devices", {
      headers: {
        spotifyAuthorization: `Bearer ${accessToken}`,
      },
    });
    const response = await axios.post(
      "spotify/play-track",
      {
        device_id: device.data.devices[0].id,
        uri: trackUri,
      },
      {
        headers: {
          spotifyAuthorization: `Bearer ${accessToken}`,
        },
      },
    );
    if (response.status === 200) {
      console.log("Track is playing");
    } else {
      console.log("Failed to play track", response.status);
    }
  } catch (error) {
    console.error("Error playing track", error);
  }
};
const fetchCurrentUser = async () => {
  try {
    const response = await axios.get("/user");
    currentUser.value = response.data;
  } catch (error) {
    console.error("Error fetching current user", error);
  }
};
const deletePost = async (postId: number) => {
  if (!confirm("本当にこの投稿を削除しますか？")) {
    return;
  }
  try {
    const response = await axios.delete(`spotify/posts/${postId}`);
    if (response.status === 200) {
      posts.value = posts.value.filter((post) => post.id !== postId);
      alert("削除が完了しました");
      console.log("Post deleted successfully");
    } else {
      console.log("Failed to delete post", response.status);
    }
  } catch (error) {
    console.error("Error deleting post", error);
  }
};

// 各投稿ごとのメニューとボタンを管理するオブジェクト
const menuRefs = ref<Record<number, HTMLElement | null>>({});
const menuButtonRefs = ref<Record<number, HTMLElement | null>>({});
const openMenuId = ref<number | null>(null);

const toggleMenu = (postId: number) => {
  if (openMenuId.value === postId) {
    openMenuId.value = null;
  } else {
    openMenuId.value = postId;
  }
};

const isMenuOpen = (postId: number) => {
  return openMenuId.value === postId;
};

// メニュー外をクリックしたときに閉じる処理
const handleClickOutside = (event: MouseEvent) => {
  const menuElement = menuRefs.value[openMenuId.value!];
  const menuButtonElement = menuButtonRefs.value[openMenuId.value!];

  if (
    menuElement &&
    !menuElement.contains(event.target as Node) &&
    menuButtonElement &&
    !menuButtonElement.contains(event.target as Node)
  ) {
    openMenuId.value = null; // メニューを閉じる
  }
};
</script>

<style scoped></style>
