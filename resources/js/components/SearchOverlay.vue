<script setup>
/**
 * SearchOverlay.vue - 搜索覆盖层组件
 *
 * 功能说明：
 * - 全屏搜索界面
 * - 实时搜索文章、视频、项目、资源等内容
 * - 根据内容类型跳转到正确的路由
 * - 键盘导航支持
 *
 * 技术实现：
 * - 使用 computed 属性实现实时过滤
 * - 键盘事件监听（Escape 关闭）
 * - 点击遮罩层关闭搜索
 *
 * 使用示例：
 * <SearchOverlay :is-open="isSearchOpen" :posts="searchResults" @close="closeSearch" />
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Search, X, ArrowUpRight } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  posts: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['close']);

const { t } = useI18n();
const searchQuery = ref('');

const filteredPosts = computed(() => {
  if (!searchQuery.value.trim()) return [];
  const query = searchQuery.value.toLowerCase();
  return props.posts.filter(post =>
    post.title.toLowerCase().includes(query) ||
    post.excerpt.toLowerCase().includes(query)
  ).slice(0, 5);
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    document.body.classList.add('overflow-hidden');
  } else {
    document.body.classList.remove('overflow-hidden');
    searchQuery.value = '';
  }
});

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.isOpen) {
    emit('close');
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  document.body.classList.remove('overflow-hidden');
});

const closeSearch = () => {
  emit('close');
};
</script>

<template>
  <Transition name="search-fade">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-[70] flex items-center justify-center p-4 md:p-8 bg-black/80 backdrop-blur-md"
      @click.self="closeSearch"
    >
      <div class="w-full max-w-4xl bg-white relative" @click.stop>
        <button
          @click="closeSearch"
          class="absolute right-4 top-4 hover:text-accent transition-colors z-10"
        >
          <X class="w-6 h-6 md:w-8 md:h-8" />
        </button>

        <div class="flex flex-col gap-6 md:gap-8 p-6 md:p-8">
          <span class="text-xs font-bold tracking-[0.4em] text-accent uppercase">
            {{ t('direct_blog_query') || 'DIRECT BLOG QUERY' }}
          </span>

          <div class="relative">
            <Search class="absolute left-4 md:left-6 top-1/2 -translate-y-1/2 w-6 h-6 md:w-8 md:h-8 text-black/20" />
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="t('search_placeholder') || 'SEARCH ARCHYX...'"
              class="w-full bg-construct-paper border-4 border-black py-6 md:py-8 pl-14 md:pl-16 pr-8 text-xl md:text-2xl font-display tracking-tight focus:outline-none focus:bg-white transition-all"
              autofocus
            />
          </div>

          <div v-if="searchQuery.trim()" class="space-y-3 md:space-y-4 max-h-[40vh] overflow-y-auto pr-2">
            <span class="text-[10px] font-bold tracking-[0.2em] opacity-40 uppercase block">
              {{ t('matching_artifacts') || 'MATCHING ARTIFACTS' }}
            </span>

            <template v-if="filteredPosts.length > 0">
              <Link
                v-for="post in filteredPosts"
                :key="`${post.type}-${post.id}`"
                :href="post.route || `/blog/${post.id}`"
                @click="closeSearch"
                class="group flex justify-between items-center p-3 md:p-4 border-2 border-transparent hover:border-black hover:bg-construct-paper transition-all"
              >
                <div>
                  <span class="text-[10px] font-bold tracking-widest text-accent block uppercase mb-1">
                    {{ post.category || post.type }}
                  </span>
                  <h4 class="font-display text-lg md:text-xl tracking-tight leading-none">
                    {{ post.title }}
                  </h4>
                </div>
                <ArrowUpRight class="w-5 h-5 md:w-6 md:h-6 opacity-0 group-hover:opacity-100 transition-opacity shrink-0" />
              </Link>
            </template>

            <div
              v-else
              class="p-6 md:p-8 text-center text-black/40 text-xs md:text-sm font-bold tracking-widest uppercase border-4 border-dashed border-black/10"
            >
              {{ t('no_materials_found') || 'NO EXPERIMENTS FOUND ALIGNING WITH QUERY' }}
            </div>
          </div>

          <div class="flex justify-between items-center text-[10px] md:text-xs font-bold tracking-[0.2em] opacity-40 uppercase">
            <span>{{ t('press_esc') || 'PRESS ESC' }}</span>
            <span>v.01.2 ARCHITECTURE</span>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style lang="scss" scoped>
.search-fade {
  &-enter-active,
  &-leave-active {
    transition: opacity 0.2s ease;
  }

  &-enter-from,
  &-leave-to {
    opacity: 0;
  }
}
</style>
