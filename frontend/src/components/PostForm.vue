<!-- components/PostForm.vue -->
<template>
  <div>
    <h2>Post a new track</h2>
    <form @submit.prevent="submitPost">
      <div>
        <label for="track-search">Search Spotify:</label>
        <input v-model="searchQuery" id="track-search" type="text" @input="searchTracks" placeholder="Enter track name or artist">
        <ul v-if="searchResults.length">
          <li v-for="track in searchResults" :key="track.id" @click="selectTrack(track)">
            {{ track.name }} by {{ track.artists[0].name }}
          </li>
        </ul>
      </div>
      <div v-if="selectedTrack">
        <h3>Selected Track</h3>
        <p>{{ selectedTrack.name }} by {{ selectedTrack.artists[0].name }}</p>
        <img :src="selectedTrack.album.images[0]?.url" alt="Album Art" class="w-20 h-20">
      </div>
      <button type="submit">Post</button>
    </form>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import axios from 'axios';
import { usePostStore } from '@/stores';
import { SpotifyTrack, Track } from '@/types';

const searchQuery = ref<string>('');
const searchResults = ref<SpotifyTrack[]>([]);
const selectedTrack = ref<SpotifyTrack | null>(null);
const postStore = usePostStore();

const searchTracks = async () => {
  if (searchQuery.value.length > 2) {
    const response = await axios.get(`/spotify/search?query=${searchQuery.value}`);
    searchResults.value = response.data.tracks.items;
  }
};

const selectTrack = (track: SpotifyTrack) => {
  selectedTrack.value = track;
};

const submitPost = async () => {
  if (!selectedTrack.value) return;

  const postData: Track = {
    spotify_track_id: selectedTrack.value.id,
    track_name: selectedTrack.value.name,
    artist_name: selectedTrack.value.artists[0].name,
    album_name: selectedTrack.value.album.name,
    album_image_url: selectedTrack.value.album.images[0]?.url || '',
  };

  await postStore.createPost(postData);
  selectedTrack.value = null;
  searchQuery.value = '';
  searchResults.value = [];
};
</script>
