<script setup>
/**
 * ContentCard.vue - 后台内容管理通用卡片
 * 
 * 适用于 Posts / Projects / Videos 三个管理页面的卡片展示。
 * 差异部分通过具名插槽注入：
 *   #top-left-badges     - 左上角标签（如 Post 的 status + category）
 *   #top-right-badge     - 右上角标签（如 Project 的 status）
 *   #bottom-right-badge  - 右下角标签（如 Video 的 status）
 *   #actions-before      - 操作按钮前置内容（如 Project 的 GitHub/Demo 链接）
 */
import { computed, useSlots } from 'vue';
import { Clock, Edit3, Globe, Trash2 } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { isDarkMode } = useTheme();
const slots = useSlots();

const props = defineProps({
  /** 数据源对象 */
  item: { type: Object, required: true },
  /** 封面图片 URL */
  coverImage: { type: String, default: '' },
  /** 卡片标题 */
  title: { type: String, default: '' },
  /** 卡片描述 */
  description: { type: String, default: '' },
  /** 标签数组 */
  tags: { type: Array, default: () => [] },
  /** 格式化后的日期字符串 */
  date: { type: String, default: '' },
  /** 作者名称 */
  author: { type: String, default: '' },
  /** 无图片时的占位图标组件 */
  placeholderIcon: { type: [Object, Function], default: null },
});

const emit = defineEmits(['edit', 'seo-edit', 'delete']);

const hasTopLeftBadges = computed(() => !!slots['top-left-badges']);
const hasTopRightBadge = computed(() => !!slots['top-right-badge']);
const hasBottomRightBadge = computed(() => !!slots['bottom-right-badge']);
const hasActionsBefore = computed(() => !!slots['actions-before']);

const cardBg = computed(() => isDarkMode.value ? 'bg-gray-800' : 'bg-white');
const placeholderBg = computed(() => isDarkMode.value ? 'bg-gray-900' : 'bg-gray-100');
const placeholderText = computed(() => isDarkMode.value ? 'text-gray-600' : 'text-gray-400');
const titleColor = computed(() => isDarkMode.value ? 'text-white' : 'text-gray-900');
const descColor = computed(() => isDarkMode.value ? 'text-gray-400' : 'text-gray-600');
const metaColor = computed(() => isDarkMode.value ? 'text-gray-500' : 'text-gray-400');
const tagClass = computed(() => isDarkMode.value ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600');
const tagOverflow = computed(() => isDarkMode.value ? 'bg-gray-700 text-gray-400' : 'bg-gray-100 text-gray-500');
const editBtn = computed(() => isDarkMode.value ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100');
const seoBtn = computed(() => isDarkMode.value ? 'text-gray-400 hover:text-green-400 hover:bg-gray-700' : 'text-gray-500 hover:text-green-500 hover:bg-gray-100');
const deleteBtn = computed(() => isDarkMode.value ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100');

const thumbnailBorder = computed(() => {
  if (!props.coverImage && !isDarkMode.value) return 'border-2 border-b-0 border-dashed border-gray-300';
  if (!props.coverImage && isDarkMode.value) return 'border-2 border-b-0 border-dashed border-gray-600';
  return '';
});
</script>

<template>
  <div :class="['flex flex-col rounded-lg shadow-md', cardBg]">
    <!-- ═══════ 缩略图区域 ═══════ -->
    <div :class="['relative aspect-video box-border rounded-t-lg overflow-hidden', thumbnailBorder, placeholderBg]">
      <!-- 有图片 -->
      <img
        v-if="coverImage"
        :src="coverImage"
        :alt="title"
        class="w-full h-full object-cover"
      />
      <!-- 无图片占位 -->
      <div v-else class="w-full h-full flex items-center justify-center">
        <component
          v-if="placeholderIcon"
          :is="placeholderIcon"
          :class="['w-12 h-12', placeholderText]"
        />
      </div>

      <!-- 左上角标签 -->
      <div v-if="hasTopLeftBadges" class="absolute top-2 left-2 flex gap-1">
        <slot name="top-left-badges" />
      </div>

      <!-- 右上角标签 -->
      <div v-if="hasTopRightBadge" class="absolute top-2 right-2">
        <slot name="top-right-badge" />
      </div>

      <!-- 右下角标签 -->
      <div v-if="hasBottomRightBadge" class="absolute bottom-2 right-2">
        <slot name="bottom-right-badge" />
      </div>
    </div>

    <!-- ═══════ 信息区域 ═══════ -->
    <div class="p-4 flex flex-col flex-1">
      <!-- 标题 -->
      <h3
        :class="['font-bold mb-2 text-lg', titleColor]"
        style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden;"
      >{{ title }}</h3>

      <!-- 描述 -->
      <p
        :class="['text-sm mb-4', descColor]"
        style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden; min-height: 2.5rem;"
      >{{ description }}</p>

      <!-- 标签 -->
      <div class="flex flex-wrap gap-1 mb-4">
        <span
          v-for="tag in tags.slice(0, 3)"
          :key="tag"
          :class="['text-xs px-2 py-1 rounded', tagClass]"
        >
          {{ tag }}
        </span>
        <span v-if="tags.length > 3" :class="['text-xs px-2 py-1 rounded', tagOverflow]">
          +{{ tags.length - 3 }}
        </span>
      </div>

      <!-- ═══════ 底部操作栏 ═══════ -->
      <div class="flex items-center justify-between mt-auto">
        <div :class="['flex items-center gap-2 text-xs', metaColor]">
          <Clock size="12" />
          {{ date }}
          <span class="ml-2">{{ author }}</span>
        </div>

        <div class="flex gap-2">
          <!-- 前置操作（如 Project 的 GitHub/外部链接） -->
          <slot name="actions-before" />

          <!-- 编辑 -->
          <button
            @click="emit('edit', item)"
            :class="['p-2 transition-colors rounded', editBtn]"
          >
            <Edit3 size="14" />
          </button>

          <!-- SEO 编辑 -->
          <button
            @click="emit('seo-edit', item)"
            :class="['p-2 transition-colors rounded', seoBtn]"
          >
            <Globe size="14" />
          </button>

          <!-- 删除 -->
          <button
            @click="emit('delete', item.id)"
            :class="['p-2 transition-colors rounded', deleteBtn]"
          >
            <Trash2 size="14" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
