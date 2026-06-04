<script setup>
/**
 * SeoForm.vue ─ 公共 SEO 编辑弹窗
 *
 * 文章/视频/项目卡片的 Globe 按钮触发此弹窗。
 *
 * Props:
 *   v-model:visible — 控制弹窗显隐
 *   :item           — { id, meta_title, meta_description, meta_keywords }
 *   resourceRoute   — Inertia 路由名，如 'posts.update'
 *
 * Emits:
 *   update:visible  — 关闭弹窗
 *   saved           — 保存成功后回调
 */
import { watch, ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Globe, X } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';

const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const props = defineProps({
  visible: { type: Boolean, default: false },
  item: {
    type: Object,
    default: () => ({ id: null, meta_title: '', meta_description: '', meta_keywords: '' }),
  },
  resourceRoute: { type: String, required: true },
});

const emit = defineEmits(['update:visible', 'saved']);

// ── 表单数据 ──
const seo = ref({ meta_title: '', meta_description: '', meta_keywords: '' });

const reset = () => {
  seo.value = {
    meta_title: props.item?.meta_title || '',
    meta_description: props.item?.meta_description || '',
    meta_keywords: props.item?.meta_keywords || '',
  };
};

watch(() => props.visible, (v) => { if (v) reset(); });

const saving = ref(false);

const handleSave = () => {
  if (!props.item?.id || saving.value) return;
  saving.value = true;

  router.put(route(props.resourceRoute, props.item.id), { ...seo.value }, {
    preserveState: true,
    onSuccess: () => {
      toastSuccess('SEO 设置已保存');
      saving.value = false;
      emit('saved');
      emit('update:visible', false);
    },
    onError: (err) => {
      toastError(err?.message || 'SEO 保存失败');
      saving.value = false;
    },
  });
};

const close = () => emit('update:visible', false);

// ── 样式（对齐 FrontMenu 表单风格）──
const inputClass = computed(() => [
  'w-full px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
  isDarkMode.value
    ? 'bg-gray-900 border-gray-700 text-white placeholder-gray-500'
    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400',
]);

const labelClass = computed(() => [
  'block text-xs font-bold mb-1.5 uppercase tracking-widest',
  isDarkMode.value ? 'text-gray-400' : 'text-gray-500',
]);
</script>

<template>
  <div
    v-if="visible"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    @click.self="close"
  >
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div
      :class="[
        'relative w-full max-w-lg rounded-xl shadow-2xl overflow-hidden border',
        isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200',
      ]"
    >
      <!-- Header -->
      <div
        :class="[
          'flex items-center justify-between px-6 py-4 border-b',
          isDarkMode ? 'border-gray-700' : 'border-gray-200',
        ]"
      >
        <div class="flex items-center gap-3">
          <Globe class="text-construct-red" :size="20" />
          <h3 :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">
            SEO 设置
          </h3>
        </div>
        <button
          @click="close"
          :class="[
            'p-1.5 rounded-lg transition-colors',
            isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700',
          ]"
        >
          <X :size="18" />
        </button>
      </div>

      <!-- Body -->
      <div class="px-6 py-5 space-y-5">
        <!-- Meta Title -->
        <div>
          <label :class="labelClass">
            <Globe :size="12" class="inline mr-1.5 opacity-50" />Meta Title
          </label>
          <input
            v-model="seo.meta_title"
            type="text"
            maxlength="255"
            :class="inputClass"
            placeholder="SEO 页面标题"
          />
        </div>

        <!-- Meta Description -->
        <div>
          <label :class="labelClass">Meta Description</label>
          <textarea
            v-model="seo.meta_description"
            maxlength="500"
            rows="3"
            :class="[...inputClass, 'resize-none font-sans', isDarkMode ? 'bg-gray-900' : 'bg-white']"
            placeholder="SEO 页面描述（建议 150 字符以内）"
          ></textarea>
        </div>

        <!-- Meta Keywords -->
        <div>
          <label :class="labelClass">Meta Keywords</label>
          <input
            v-model="seo.meta_keywords"
            type="text"
            maxlength="500"
            :class="inputClass"
            placeholder="keyword1, keyword2, keyword3"
          />
        </div>
      </div>

      <!-- Footer -->
      <div
        :class="[
          'flex justify-end gap-3 px-6 py-4 border-t',
          isDarkMode ? 'border-gray-700' : 'border-gray-200',
        ]"
      >
        <button
          @click="close"
          :class="[
            'px-6 py-2.5 border flex items-center gap-2 font-bold text-xs tracking-wider uppercase transition-colors rounded-lg',
            isDarkMode
              ? 'border-gray-700 text-gray-300 hover:bg-gray-700'
              : 'border-gray-300 text-gray-700 hover:bg-gray-100',
          ]"
        >
          取消
        </button>
        <button
          @click="handleSave"
          :disabled="saving"
          class="flex items-center gap-2 px-8 py-2.5 bg-construct-red text-white font-bold tracking-widest uppercase text-xs hover:bg-red-700 transition-colors rounded-lg shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ saving ? '保存中...' : '保存' }}
        </button>
      </div>
    </div>
  </div>
</template>
