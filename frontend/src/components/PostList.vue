<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Posts</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="post in posts" :key="post.id" class="mb-4 p-4 border-b border-gray-200 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-semibold">{{ post.track.track_name }} by {{ post.track.artist_name }}</h3>
        <LikeButton
            :postId="post.id"
            :initialLikes="post.likes"
        />
        <img :src="post.track.album_image_url" alt="Album Art" class="w-20 h-20 mt-2">
        <button @click="playTrack(post.track.uri)" class="mt-2 bg-blue-500 text-white py-1 px-3 rounded">
          Play
        </button>
        <button v-if="currentUser && currentUser.id === post.user.id" @click="deletePost(post.id)" class="mt-2 bg-red-600 text-white py-1 px-3 rounded">
          delete
        </button>
        <p class="text-gray-500 mt-2">Posted by {{ post.user.name }}</p>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue'
import { usePostStore } from '@/stores/postStore'
import { Post } from '@/types'
import LikeButton from '@/components/LikeButton.vue'
import axios from 'axios'

const postStore = usePostStore()
const posts = ref<Post[]>([])
const currentUser = ref<{ id: number, name: string } | null>(null)

onMounted(async () => {
  await postStore.fetchPosts()
  await fetchCurrentUser()
})

// postStoreのpostsが更新されたときに自動的に反映されるようにwatchを使う
watch(() => postStore.posts, (newPosts) => {
  posts.value = newPosts
})

const playTrack = async (trackUri: string) => {
  try {
    const accessToken = sessionStorage.getItem('spotify_access_token')
    if (!accessToken) {
      console.error('Access token not found')
      return
    }
    const device = await axios.get('spotify/devices', {
      headers: {
        spotifyAuthorization: `Bearer ${accessToken}`
      }
    })
    const response = await axios.post('spotify/play-track', {
      device_id: device.data.devices[0].id,
      uri: trackUri
    },
    {
      headers: {
        spotifyAuthorization: `Bearer ${accessToken}`
      }
    }
    )
    if (response.status === 200) {
      console.log('Track is playing')
    } else {
      console.log('Failed to play track', response.status)
    }
  } catch (error) {
    console.error('Error playing track', error)
  }
}
const fetchCurrentUser = async () => {
  try {
    const response = await axios.get('/user')
    currentUser.value = response.data
  } catch (error) {
    console.error('Error fetching current user', error)
  }
}
const deletePost = async (postId: number) => {
  if (!confirm('本当にこの投稿を削除しますか？')) {
    return
  }
  try {
    const response = await axios.delete(`spotify/posts/${postId}`)
    if (response.status === 200) {
      posts.value = posts.value.filter(post => post.id !== postId)
      alert('削除が完了しました')
      console.log('Post deleted successfully')
    } else {
      console.log('Failed to delete post', response.status)
    }
  } catch (error) {
    console.error('Error deleting post', error)
  }
}

</script>

<style scoped>
</style>
