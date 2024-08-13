<script>
  import {defineComponent} from "vue";
  import PostList from "@/components/PostList.vue";
  import PostForm from "@/components/PostForm.vue";

  export default defineComponent({
    components: {PostForm, PostList}
  })


</script>
<template>
  <PostForm></PostForm>
  <PostList></PostList>
</template>

