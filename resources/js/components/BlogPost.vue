<!-- resources/js/components/BlogPost.vue -->
<template>

   <div class="max-w-4xl mx-auto p-8 bg-white shadow-md rounded-lg">
    <h1 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">{{message}}</h1>
    <h1 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">{{message1}}</h1>
    <p class="text-gray-600 text-sm sm:text-base md:text-lg">This is a sample blog post using Vue 3 and Tailwind CSS.</p>
  </div>
</template>

<script setup>
import { ref } from "vue";

const message = ref("Welcome to the Laravel + Vue Blog!");
const message1 = ref("欢迎访问我的博客");
</script>

<style scoped>
/* h1 {
    color: #2d3748;
} */
</style>
