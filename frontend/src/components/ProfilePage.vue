<script lang="ts" setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";
import PageTitle from "@/components/PageTitle.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

const spotifyAuthUrl = ref("");
const accessToken = ref<string | null>(null);
const userProfile = ref<any>(null);
const router = useRouter();
const route = useRoute();

const loading = ref(true);

// 初回認証用のSpotify認証URLを取得する関数
const fetchSpotifyAuthUrl = async () => {
  try {
    const response = await axios.get("/spotify/auth-url");
    spotifyAuthUrl.value = response.data.url;
  } catch (error) {
    console.error("Error fetching Spotify auth URL:", error);
  }
};

// Spotify認証後にリダイレクトされる際のコールバック処理
const handleSpotifyCallback = async () => {
  const code = route.query.code as string;
  if (code) {
    try {
      const response = await axios.get(`/spotify/callback?code=${code}`);
      const token = response.data.access_token;
      const refreshToken = response.data.refresh_token; // 追加: refresh_tokenの取得
      sessionStorage.setItem("spotify_access_token", token);
      localStorage.setItem("spotify_refresh_token", refreshToken); // 追加: refresh_tokenの保存
      accessToken.value = token;
      await getUserProfile();
      await router.replace({ path: "/profile", query: {} });
    } catch (error) {
      console.error("Error handling Spotify callback:", error);
    }
  } else {
    const sessionToken = sessionStorage.getItem("spotify_access_token");
    if (sessionToken) {
      accessToken.value = sessionToken;
      await getUserProfile();
    }
  }
};

// プロフィール情報を取得する関数
const getUserProfile = async () => {
  if (accessToken.value) {
    try {
      const response = await axios.get("/spotify/user-profile", {
        headers: {
          spotifyAuthorization: `Bearer ${accessToken.value}`,
        },
      });
      if (!response.data.error) {
        userProfile.value = response.data;
      }
    } catch (error) {
      console.error("Error fetching user profile:", error);
    }
  }
};

// コンポーネントがマウントされた際の処理
onMounted(async () => {
  await fetchSpotifyAuthUrl();
  await handleSpotifyCallback();
  loading.value = false;
});
</script>
<template>
  <div class="h-screen">
    <PageTitle title="Profile" />
    <LoadingSpinner v-if="loading" :loading="loading" />
      <!-- 初回認証時のボタン -->
    <div v-else-if="!userProfile" class="flex justify-center">
        <button class="btn btn-primary">
          <a :href="spotifyAuthUrl">
            Authenticate with Spotify
          </a>
        </button>
    </div>
      <!-- 認証後にプロフィール情報を表示 -->
      <div v-if="userProfile" class="bg-gray-100 p-3 text-black rounded-lg mx-2">
        <p><strong>Name:</strong> {{ userProfile.display_name }}</p>
        <p><strong>Email:</strong> {{ userProfile.email }}</p>
        <p><strong>Country:</strong> {{ userProfile.country }}</p>
        <img
            :src="userProfile.images[0]?.url"
            alt="Profile"
            v-if="userProfile.images?.length"
        />
      </div>
    </div>
</template>
<style scoped>
.btn {
  display: inline-block;
  padding: 10px 20px;
  color: #fff;
  background-color: #1db954;
  border: none;
  border-radius: 5px;
  text-decoration: none;
  text-align: center;
}
</style>
