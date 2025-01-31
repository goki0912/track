<template>
  <div>
    <!-- 開くボタン -->
    <button @click="openModal" class="btn btn-accent btn-sm shadow-md hover:shadow-lg rounded-lg transition duration-300 flex items-center space-x-1 px-4">
      <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-5 h-5 text-white"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      <span class="text-white">New Entry</span>
    </button>

    <!-- モーダル -->
    <BaseModal :isOpen="isModalOpen" @close="closeModal">
      <h2 class="text-2xl font-bold mb-4">Post a new track</h2>
      <form @submit.prevent="submitPost" class="space-y-4">
        <!-- トラック検索 -->
        <div>
          <label for="track-search" class="label">
            <span class="label-text text-black">Search:</span>
          </label>
          <input
              v-model="searchQuery"
              id="track-search"
              type="text"
              @input="searchTracks"
              placeholder="Enter track name or artist"
              class="input input-bordered w-full bg-gray-100 text-black input-primary"
          />
          <ul v-if="searchResults.length" class="menu bg-gray-200 shadow-lg mt-2 overflow-y-auto">
            <li
                v-for="track in searchResults"
                :key="track.id"
                @click="selectTrack(track)"
                class="hover:bg-gray-300 cursor-pointer mb-1"
            >
              {{ track.name }} by {{ track.artists[0].name }}
            </li>
          </ul>
        </div>

        <!-- 選択されたトラックの表示 -->
        <div v-if="selectedTrack" class="card shadow-sm bg-gray-300">
          <div class="card-body bg-gray-200 p-4 rounded">
            <h3 class="card-title text-lg font-bold">Selected Track</h3>
            <div class="flex items-center space-x-4">
              <img
                  :src="selectedTrack.album.images[0]?.url"
                  alt="Album Art"
                  class="w-20 h-20 rounded-lg"
              />
              <div>
                <p class="text-xl">{{ selectedTrack.name }}</p>
                <p class="">  by {{ selectedTrack.artists[0].name }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 投稿ボタン -->
        <button type="submit" class="btn btn-primary w-full text-lg text-white">
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
import { useRoute } from "vue-router";
import { SpotifyTrack, Track } from "@/types";
import BaseModal from "@/components/BaseModal.vue";

const searchQuery = ref<string>("");
const searchResults = ref<SpotifyTrack[]>([]);
const selectedTrack = ref<SpotifyTrack | null>(null);
const postStore = usePostStore();
const isModalOpen = ref(false);

const route = useRoute();
const themeId: number = Number(route.params.id);

const searchTracks = async () => {
  if (searchQuery.value.length > 1) {
    const response = await axios.get(
        `api/spotify/search?query=${searchQuery.value}`,
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

  await postStore.createPost(themeId, postData);
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
