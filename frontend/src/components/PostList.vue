<template>
  <div @click="handleClickOutside" class="h-screen">
    <PageTitle :title="'Post'" />
    <div class="flex items-center justify-center text-center gap-3 mb-3">
      <p
        class="text-xl font-semibold text-secondary bg-secondary bg-opacity-20 inline-block px-4 py-1 rounded-lg shadow-sm text-center"
      >
        {{ themeTitle }}
      </p>
      <ReloadButton @reload="refreshPosts" />
    </div>
    <LoadingSpinner v-if="loading" :loading="loading" />
    <div v-else>
      <div
        class="items-center p-4 border-b border-gray-100 rounded-lg mx-2 mb-6 bg-gray-100 text-black"
      >
        <div class="flex justify-center mb-3">
          <p class="text-primary bg-gray-300 text-center font-bold text-lg w-1/2 rounded-full">Your Entry</p>
        </div>
        <div v-if="currentUserPost" class="flex">
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
        <div v-else class="flex justify-between">
          <p>You have no entry.</p>
          <PostForm></PostForm>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="post in posts"
          :key="post.id"
          class="flex items-center p-4 border-b border-gray-200 rounded-lg shadow-md mx-2"
          :class="
            currentUser && currentUser.id === post.user.id
              ? 'bg-emerald-100'
              : 'bg-white'
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
            <h3 class="text-base font-semibold text-black">
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
              class="ml-2 text-black"
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
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { usePostStore } from "@/stores/postStore";
import { useThemeStore } from "@/stores/themeStore";
import axios from "axios";
import { useRoute } from "vue-router";
import LikeButton from "@/components/LikeButton.vue";
import ReloadButton from "@/components/ReloadButton.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import PostForm from "@/components/PostForm.vue";
import { useToast } from "vue-toast-notification";
import PageTitle from "@/components/PageTitle.vue";

// ストアの使用と状態管理
const postStore = usePostStore();
const themeStore = useThemeStore();
const toast = useToast();
const posts = computed(() => postStore.posts);
const currentUser = ref<{ id: number; name: string } | null>(null);
const currentUserPost = computed(() => {
  return posts.value.find((post) => post.user_id === currentUser.value?.id);
});
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
let echoChannel;
onMounted(async () => {
  try {
    await fetchCurrentUser();
    await themeStore.fetchThemes();
    themeTitle.value =
      themeStore.themes.find((theme) => theme.id === themeId)?.title ||
      "Unknown Theme";
    await postStore.fetchPosts(themeId);

    // Reverbチャンネルで「いいね」や投稿の更新をリアルタイムでリッスン
    echoChannel = window.Echo.channel(`theme.${themeId}`).listen(
      "PostUpdated",
      (event) => {
        // イベントで受け取った投稿を更新
        const updatedPost = posts.value.find(
          (post) => post.id === event.postId,
        );
        if (updatedPost) {
          updatedPost.likes_count = event.likesCount;
        }
      },
    );
    // .listen('PostCreated', (event) => {
    //   // 新しい投稿が作成されたらリストに追加
    //   posts.value.unshift(event.newPost);
    // });
  } catch (error) {
    console.error("Error during initialization", error);
  } finally {
    loading.value = false;
  }
});

onBeforeUnmount(() => {
  if (echoChannel) {
    echoChannel.unsubscribe();
  }
});

const refreshPosts = async () => {
  loading.value = true;
  await postStore.fetchPosts(themeId);
  loading.value = false;
};

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
      toast.success("Your track is playing!", {
        position: "top-right",
      });
    } else {
      console.log("Failed to play track", response.status);
      toast.error("Failed to play a track", {
        position: "top-right",
      });
    }
  } catch (error) {
    console.error("Error playing track", error);
    toast.error("Failed to play a track", {
      position: "top-right",
    });
  }
};

// 投稿を削除する関数
const deletePost = async (postId: number) => {
  if (!confirm("本当にこの投稿を削除しますか？")) {
    return;
  }
  await postStore.deletePost(themeId, postId);
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
const loading = ref(true);
</script>

<style scoped></style>
