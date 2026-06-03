<script setup>
/**
 * AdPopup.vue - 弹窗广告组件
 * 
 * 功能说明：
 * - 首次访问显示弹窗广告
 * - 带关闭按钮，关闭后不再显示
 * - 使用 sessionStorage 记录关闭状态（每个标签页独立）
 * - 支持键盘 ESC 关闭
 * 
 * 使用方式：
 * <AdPopup />
 * 
 * 支持通过 props 传入数据（推荐）：
 * <AdPopup :ads="props.ads" />
 */
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { useAdSlot } from '../../composables/useAdSlot';

const props = defineProps({
  // 可选：通过 props 传入广告数据
  ads: {
    type: Array,
    default: null
  }
});

const pageProps = usePage().props;
const { getSingleAd } = useAdSlot({
  ads: props.ads ?? pageProps.frontAds ?? []
});

const isVisible = ref(false);

const popupAd = computed(() => {
  return getSingleAd('popup');
});

const hasSeenPopup = () => {
  return sessionStorage.getItem('ad_popup_closed') === 'true';
};

const setSeenPopup = () => {
  sessionStorage.setItem('ad_popup_closed', 'true');
};

const closePopup = () => {
  isVisible.value = false;
  setSeenPopup();
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && isVisible.value) {
    closePopup();
  }
};

onMounted(() => {
  if (!hasSeenPopup() && popupAd.value) {
    setTimeout(() => {
      isVisible.value = true;
    }, 3000);
  }
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
  <Teleport to="body">
    <Transition name="popup">
      <div
        v-if="isVisible && popupAd"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
      >
        <!-- 遮罩层 -->
        <div
          class="absolute inset-0 bg-black/70 backdrop-blur-sm"
          @click="closePopup"
        ></div>
        
        <!-- 弹窗内容 -->
        <div class="relative bg-white border-8 border-construct-black shadow-2xl max-w-md w-full overflow-hidden">
          <!-- 关闭按钮 -->
          <button
            @click="closePopup"
            class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center bg-black text-white hover:bg-construct-red transition-colors"
            aria-label="Close ad popup"
          >
            <X class="w-5 h-5" />
          </button>
          
          <!-- 广告内容 -->
          <a
            :href="popupAd.link_url"
            target="_blank"
            rel="noopener noreferrer"
            class="block"
          >
            <div class="aspect-[3/4] bg-construct-black overflow-hidden">
              <img
                :src="popupAd.image_url"
                :alt="popupAd.title"
                class="w-full h-full object-cover"
              />
            </div>
            <div class="p-6 bg-construct-black">
              <span class="text-[10px] font-black tracking-widest text-construct-red uppercase">
                SPONSORED
              </span>
              <h3 class="font-display text-xl tracking-tight text-white mt-2">
                {{ popupAd.title }}
              </h3>
            </div>
          </a>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.popup-enter-active,
.popup-leave-active {
  transition: all 0.3s ease;
}

.popup-enter-from,
.popup-leave-to {
  opacity: 0;
}

.popup-enter-from .relative,
.popup-leave-to .relative {
  transform: scale(0.9);
}
</style>