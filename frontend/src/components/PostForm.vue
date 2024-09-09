<template>
  <div>
    <button @click="openModal" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white p-4 rounded-full">
      POST A TRACK
    </button>

    <BaseModal :isOpen="isModalOpen" @close="closeModal">
      <h2 class="text-xl font-bold mb-4">Post a new track</h2>
      <form @submit.prevent="submitPost" class="space-y-6">
        <div>
          <label
            for="track-search"
            class="block text-sm font-medium text-gray-700 mb-2"
            >Search Spotify:</label
          >
          <input
            v-model="searchQuery"
            id="track-search"
            type="text"
            @input="searchTracks"
            placeholder="Enter track name or artist"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
          <ul
            v-if="searchResults.length"
            class="mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
          >
            <li
              v-for="track in searchResults"
              :key="track.id"
              @click="selectTrack(track)"
              class="px-4 py-2 cursor-pointer hover:bg-gray-100"
            >
              {{ track.name }} by {{ track.artists[0].name }}
            </li>
          </ul>
        </div>
        <div
          v-if="selectedTrack"
          class="p-4 border border-gray-200 rounded-lg bg-gray-50"
        >
          <h3 class="text-lg font-semibold">Selected Track</h3>
          <div class="flex items-center space-x-4">
            <img
              :src="selectedTrack.album.images[0]?.url"
              alt="Album Art"
              class="w-20 h-20 rounded-lg"
            />
            <p>
              {{ selectedTrack.name }} by {{ selectedTrack.artists[0].name }}
            </p>
          </div>
        </div>
        <button
          type="submit"
          class="w-full px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Post
        </button>
      </form>
    </BaseModal>
  </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import axios from "axios";
import { usePostStore } from "@/stores/postStore";
import { SpotifyTrack, Track } from "@/types";
import BaseModal from "@/components/BaseModal.vue";

const searchQuery = ref<string>("");
const searchResults = ref<SpotifyTrack[]>([]);
const selectedTrack = ref<SpotifyTrack | null>(null);
const postStore = usePostStore();
const isModalOpen = ref(false);

const searchTracks = async () => {
  if (searchQuery.value.length > 1) {
    const response = await axios.get(
      `/spotify/search?query=${searchQuery.value}`,
    );
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
    album_image_url: selectedTrack.value.album.images[0]?.url || "",
    uri: selectedTrack.value.uri,
  };

  await postStore.createPost(theme_id, postData);
  closeModal();
  selectedTrack.value = null;
  searchQuery.value = "";
  searchResults.value = [];
};

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};
</script>
