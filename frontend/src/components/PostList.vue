<template>
  <div @click="handleClickOutside">
    <h2 class="text-2xl font-bold mb-4">POSTS</h2>
    <p>{{ themeTitle }}</p>
    <div
        class="items-center p-4 border-b border-gray-100 rounded-lg mx-2 mb-6 bg-gray-100"
    >
      <p>your entry</p>
      <div
          v-if="currentUserPost"
          class="flex">
        <img
            :src="currentUserPost.track.album_image_url"
            alt="Album Art"
            class="w-12 h-12 mr-4 rounded"
        />
        <div class="flex-1">
          <!-- 曲名とアーティスト名 -->
          <h3 class="text-base font-semibold">
            {{ currentUserPost.track.track_name }} <br />
            <span class="text-sm text-gray-600">{{
                currentUserPost.track.artist_name
              }}</span>
          </h3>
        </div>
      </div>
      <div v-else>
        you have no entry.
      </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="post in posts"
        :key="post.id"
        class="flex items-center p-4 border-b border-gray-200 rounded-lg shadow-md mx-2"
        :class="
          currentUser && currentUser.id === post.user.id ? 'bg-emerald-100' : 'bg-white'
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
import { ref, computed, onBeforeMount } from "vue";
import { usePostStore } from "@/stores/postStore";
import { useThemeStore } from "@/stores/themeStore";
import { Post } from "@/types";
import axios, { post } from "axios";
import { useRoute } from "vue-router";
import LikeButton from "@/components/LikeButton.vue";

// ストアの使用と状態管理
const postStore = usePostStore();
const themeStore = useThemeStore();
const posts = computed(() => postStore.posts);
const currentUser = ref<{ id: number; name: string } | null>(null);
const currentUserPost = computed(() => {
  if (posts.value.length === 0) {
    return null;
  }
  return posts.value.find((post) => post.user_id === currentUser.value?.id);
},
);
const themeTitle = ref("");

// ルートから themeId を取得
const route = useRoute();
const themeId: number = Number(route.params.id);

// ユーザー情報を取得する関数
const fetchCurrentUser = async () => {
  try {
    const response = await axios.get("/user");
    currentUser.value = response.data;
    // getCurrentUserPost();
  } catch (error) {
    console.error("Error fetching current user", error);
  }
};

// テーマと投稿を初期化する処理
onBeforeMount(async () => {
  await fetchCurrentUser();
  await themeStore.fetchThemes();
  themeTitle.value =
    themeStore.themes.find((theme) => theme.id === themeId)?.title ||
    "Unknown Theme";
  await postStore.fetchPosts(themeId);
});

// Spotifyトラックを再生する関数
const playTrack = async (trackUri: string) => {
  try {
    const accessToken = sessionStorage.getItem("spotify_access_token");
    if (!accessToken) {
      console.error("Access token not found");
      return;
    }

    const device = await axios.get("spotify/devices", {
      headers: { spotifyAuthorization: `Bearer ${accessToken}` },
    });

    const response = await axios.post(
      "spotify/play-track",
      {
        device_id: device.data.devices[0].id,
        uri: trackUri,
      },
      {
        headers: { spotifyAuthorization: `Bearer ${accessToken}` },
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

// 投稿を削除する関数
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

// 投稿モーダル管理
const menuRefs = ref<Record<number, HTMLElement | null>>({});
const menuButtonRefs = ref<Record<number, HTMLElement | null>>({});
const openMenuId = ref<number | null>(null);

// モーダルの開閉をトグルする
const toggleMenu = (postId: number) => {
  openMenuId.value = openMenuId.value === postId ? null : postId;
};

// モーダルが開いているかを確認
const isMenuOpen = (postId: number) => openMenuId.value === postId;

// モーダル外クリック時にモーダルを閉じる処理
const handleClickOutside = (event: MouseEvent) => {
  const menuElement = menuRefs.value[openMenuId.value!];
  const menuButtonElement = menuButtonRefs.value[openMenuId.value!];

  if (
    menuElement &&
    !menuElement.contains(event.target as Node) &&
    menuButtonElement &&
    !menuButtonElement.contains(event.target as Node)
  ) {
    openMenuId.value = null;
  }
};

const getCurrentUserPost = () => {
  return posts.value.filter((post) => post.user_id === currentUser.value.id);
};
</script>

<style scoped></style>
