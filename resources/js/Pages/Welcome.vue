<script setup>
import { ref } from 'vue';
import Home from './Home.vue';

const showSplash = ref(true);
const showHome = ref(false);

const handleSplashComplete = () => {
    showSplash.value = false;
    setTimeout(() => {
        showHome.value = true;
    }, 100);
};
</script>

<template>
    <div>
        <SplashScreen @complete="handleSplashComplete" v-if="showSplash" />
        <Home v-if="showHome" />
    </div>
</template>

<style scoped>
</style>