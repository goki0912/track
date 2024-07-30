<script lang="ts" setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";

const spotifyAuthUrl = ref('');
const accessToken = ref<string | null>(null);
const userProfile = ref<any>(null);
const router = useRouter();
const route = useRoute();

const getUserProfile = async () => {
  if (accessToken.value) {
    try {
      const response = await axios.get('/spotify/user-profile', {
        headers: {
          Authorization: `Bearer ${accessToken.value}`,
        },
      });
      // access tokenがexpiredした時のため
      if (!response.data.error) {
      userProfile.value = response.data;
      }
    } catch (error) {
      console.error('Error fetching user profile:', error);
    }
  }
};

// 認証URLを取得する関数
const fetchSpotifyAuthUrl = async () => {
  try {
    const response = await axios.get('/spotify/auth-url');
    spotifyAuthUrl.value = response.data.url;
  } catch (error) {
    console.error('Error fetching Spotify auth URL:', error);
  }
};

const handleSpotifyCallback = async () => {
  const code = route.query.code as string;
  if (code) {
    try {
      const response = await axios.get(`/spotify/callback?code=${code}`);
      const token = response.data.access_token;
      sessionStorage.setItem('spotify_access_token', token);
      accessToken.value = token;
      getUserProfile();
      router.replace({ path: '/profile', query: {} });
    } catch (error) {
      console.error('Error handling Spotify callback:', error);
    }
  } else {
    const sessionToken = sessionStorage.getItem('spotify_access_token');
    if (sessionToken) {
      accessToken.value = sessionToken;
      getUserProfile();
    }
  }
};

onMounted(async () => {
  await fetchSpotifyAuthUrl();
  await handleSpotifyCallback();
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-green-50">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
      <h1 class="text-3xl font-bold text-green-600 mb-4">Profile</h1>
      <a :href="spotifyAuthUrl" v-if="!userProfile" class="btn btn-primary">Authenticate with Spotify</a>
      <div v-if="userProfile">
        <h2>Spotify Profile</h2>
        <p><strong>Name:</strong> {{ userProfile.display_name }}</p>
        <p><strong>Email:</strong> {{ userProfile.email }}</p>
        <p><strong>Country:</strong> {{ userProfile.country }}</p>
        <img :src="userProfile.images[0]?.url" alt="Profile Image" v-if="userProfile.images?.length">
      </div>
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
