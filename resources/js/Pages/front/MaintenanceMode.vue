<script setup>
/**
 * MaintenanceMode.vue - 维护模式页面
 * 
 * 功能说明：
 * - 当后台开启维护模式时显示
 * - 告知用户网站正在维护中
 * - 提供预计恢复时间
 */
import { ref, onMounted } from 'vue';
import { useSiteConfig } from '../../composables/useSiteConfig';

const { getSiteName } = useSiteConfig();
const siteName = getSiteName();
const currentTime = ref(new Date());

onMounted(() => {
  setInterval(() => {
    currentTime.value = new Date();
  }, 1000);
});
</script>

<template>
  <div class="min-h-screen bg-black text-white flex flex-col items-center justify-center p-8 relative overflow-hidden">
    <!-- Background Grid -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute inset-0" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-2xl text-center">
      <!-- Logo -->
      <div class="mb-12">
        <h1 class="font-display text-6xl md:text-8xl font-black tracking-tighter text-white">
          {{ siteName }}
        </h1>
        <div class="w-24 h-1 bg-accent mx-auto mt-6"></div>
      </div>

      <!-- Maintenance Status -->
      <div class="mb-12">
        <div class="inline-flex items-center gap-3 px-6 py-3 border border-accent/50 bg-accent/10 mb-8">
          <div class="w-3 h-3 bg-accent rounded-full animate-pulse"></div>
          <span class="text-accent font-bold tracking-widest uppercase text-sm">System Maintenance</span>
        </div>

        <h2 class="font-display text-3xl md:text-4xl tracking-tight mb-6">
          网站维护中
        </h2>

        <p class="text-white/60 text-lg leading-relaxed mb-8">
          我们正在进行系统升级和维护，以提供更好的服务。<br>
          请稍后访问，感谢您的耐心等待。
        </p>

        <p class="text-white/40 text-sm tracking-widest uppercase">
          We are currently performing scheduled maintenance.<br>
          Please check back soon.
        </p>
      </div>

      <!-- Current Time -->
      <div class="mb-8">
        <p class="text-white/30 text-xs tracking-[0.3em] uppercase">
          Current Time
        </p>
        <p class="font-mono text-2xl text-white/50 mt-2">
          {{ currentTime.toLocaleTimeString('en-US', { hour12: false }) }}
        </p>
      </div>

      <!-- Decorative Elements -->
      <div class="flex items-center justify-center gap-4 text-white/20 text-xs tracking-[0.3em] uppercase">
        <span>v0.1</span>
        <span class="w-1 h-1 bg-white/20 rounded-full"></span>
        <span>Constructivist Digital Archive</span>
      </div>
    </div>

    <!-- Corner Decorations -->
    <div class="absolute top-8 left-8 w-16 h-16 border-l-2 border-t-2 border-white/10"></div>
    <div class="absolute top-8 right-8 w-16 h-16 border-r-2 border-t-2 border-white/10"></div>
    <div class="absolute bottom-8 left-8 w-16 h-16 border-l-2 border-b-2 border-white/10"></div>
    <div class="absolute bottom-8 right-8 w-16 h-16 border-r-2 border-b-2 border-white/10"></div>
  </div>
</template>
