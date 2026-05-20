<script setup>
/**
 * JournalForm.vue - 日志编辑表单组件
 * 
 * 功能说明：
 * - 编辑日志基本信息（标题、内容、心情、天气、日期、公开状态）
 */
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  BookOpen,
  X,
  Save,
  Calendar,
  Cloud,
  Smile,
  Globe,
  Lock
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useJournalData } from '../../composables/useJournalData';
import { adminUsers } from '../../data/users';

const { getMoodLabel, getWeatherLabel, getMoodTypes, getWeatherTypes } = useJournalData();

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  journalData: {
    type: Object,
    default: null
  },
  visible: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['save', 'cancel']);

const editingJournal = ref(null);
const isEditing = ref(false);

const deepClone = (obj) => {
  if (!obj) return null;
  return JSON.parse(JSON.stringify(obj));
};

watch(() => props.visible, (newVal) => {
  if (newVal && props.journalData) {
    editingJournal.value = deepClone(props.journalData);
    isEditing.value = props.journalData.action !== 'add';
  } else {
    editingJournal.value = null;
    isEditing.value = false;
  }
});

const handleCancel = () => {
  emit('cancel');
};

const handleSave = () => {
  if (!editingJournal.value?.title?.trim()) {
    alert('日志标题不能为空');
    return;
  }
  if (!editingJournal.value?.content?.trim()) {
    alert('日志内容不能为空');
    return;
  }
  
  emit('save', editingJournal.value);
};
</script>

<template>
  <Transition name="modal">
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="handleCancel" />

      <div :class="['relative w-full max-w-2xl mx-4 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col', isDarkMode ? 'bg-gray-800' : 'bg-white']">
        <div :class="['flex items-center justify-between p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <h3 :class="['text-xl font-bold flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
            <BookOpen :size="20" />
            {{ isEditing ? '编辑日志' : '新增日志' }}
          </h3>
          <button @click="handleCancel" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']">
            <X :size="20" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
          <div :class="['p-6 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">日志标题 *</label>
                <input v-model="editingJournal.title" type="text" placeholder="请输入日志标题..." :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">用户</label>
                <select v-model="editingJournal.user_id" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']">
                  <option v-for="user in adminUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
            </div>
            
            <div class="mt-4">
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">日志内容 *</label>
              <textarea v-model="editingJournal.content" rows="6" placeholder="请输入日志内容..." :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
              <div>
                <label :class="['block text-sm font-bold mb-2 flex items-center gap-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  <Smile :size="16" />
                  心情
                </label>
                <select v-model="editingJournal.mood" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']">
                  <option v-for="mood in getMoodTypes()" :key="mood" :value="mood">{{ getMoodLabel(mood) }}</option>
                </select>
              </div>
              
              <div>
                <label :class="['block text-sm font-bold mb-2 flex items-center gap-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  <Cloud :size="16" />
                  天气
                </label>
                <select v-model="editingJournal.weather" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']">
                  <option v-for="weather in getWeatherTypes()" :key="weather" :value="weather">{{ getWeatherLabel(weather) }}</option>
                </select>
              </div>
              
              <div>
                <label :class="['block text-sm font-bold mb-2 flex items-center gap-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  <Calendar :size="16" />
                  日期
                </label>
                <input v-model="editingJournal.date" type="date" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']" />
              </div>
            </div>
            
            <div class="flex items-center gap-4 mt-4">
              <div class="flex items-center">
                <input 
                  v-model="editingJournal.is_public" 
                  type="checkbox" 
                  id="isPublic"
                  :class="['mr-2 w-5 h-5 rounded focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500' : 'bg-white border-gray-300']"
                />
                <label for="isPublic" :class="['flex items-center gap-2 font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  <component :is="editingJournal.is_public ? Globe : Lock" :size="16" />
                  设为公开
                </label>
              </div>
            </div>
          </div>
        </div>

        <div :class="['flex gap-3 p-6 border-t', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <button @click="handleCancel" :class="['flex-1 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded border', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']">
            取消
          </button>
          <button @click="handleSave" class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-600 transition-colors rounded">
            <Save :size="18" />
            保存
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95);
  opacity: 0;
}
</style>
