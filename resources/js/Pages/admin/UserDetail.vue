<script setup>
/**
 * UserDetail.vue - 用户详情页面
 */
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import {
  User,
  Mail,
  ChevronLeft,
  Edit3,
  Shield
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { adminUsers } from '../../data/users';
import { findById, findIndexById } from '../../utils/typeConvert';
import UserForm from '../../components/admin/UserForm.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const router = useRouter();
const route = useRoute();

const users = ref([...adminUsers]);
const isFormVisible = ref(false);
const editingUser = ref(null);

const user = computed(() => {
  return findById(users.value, route.params.id) || null;
});

const formatToShort = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('zh-CN', { year: 'numeric', month: 'short', day: 'numeric' });
};

const getRoleStyle = (role) => {
  switch (role) {
    case 'admin':
      return isDarkMode.value ? 'bg-purple-500/20 text-purple-400 border-purple-500/50' : 'bg-purple-100 text-purple-600 border-purple-300';
    case 'editor':
      return isDarkMode.value ? 'bg-blue-500/20 text-blue-400 border-blue-500/50' : 'bg-blue-100 text-blue-600 border-blue-300';
    default:
      return isDarkMode.value ? 'bg-gray-500/20 text-gray-400 border-gray-500/50' : 'bg-gray-100 text-gray-600 border-gray-300';
  }
};

const getRoleLabel = (role) => {
  switch (role) {
    case 'admin': return t('admin_role_admin') || '管理员';
    case 'editor': return t('admin_role_editor') || '编辑';
    default: return t('admin_role_user') || '用户';
  }
};

const goBack = () => {
  router.push({ name: 'admin-users' });
};

const handleEdit = (user) => {
  editingUser.value = { ...user };
  isFormVisible.value = true;
};

const handleSave = (savedUser) => {
  const index = findIndexById(users.value, savedUser.id);
  if (index !== -1) {
    users.value[index] = savedUser;
  }
  isFormVisible.value = false;
  editingUser.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingUser.value = null;
};
</script>

<template>
  <div class="p-8">
    <div v-if="user">
      <div class="mb-8">
        <button 
          @click="goBack" 
          :class="['mb-6 flex items-center gap-2 px-4 py-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600']"
        >
          <ChevronLeft :size="20" />
          <span class="font-bold tracking-wider uppercase text-sm">{{ t('admin_users') }}</span>
        </button>
        
        <div :class="['rounded-xl p-8', isDarkMode ? 'bg-gray-800' : 'bg-white shadow-lg']">
          <div class="flex items-start gap-6 mb-8">
            <div :class="['w-24 h-24 rounded-full flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="48" />
            </div>
            <div class="flex-1">
              <h2 :class="['text-3xl font-bold mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ user.name }}</h2>
              <p :class="['text-lg', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ user.email }}</p>
              <div class="flex items-center gap-3 mt-3">
                <span :class="['px-4 py-1.5 rounded-full text-sm font-bold border', getRoleStyle(user.roleId)]">{{ getRoleLabel(user.roleId) }}</span>
                <span :class="['px-4 py-1.5 rounded-full text-sm font-bold', user.status === 'active' ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-100 text-green-600') : (isDarkMode ? 'bg-gray-600/50 text-gray-400' : 'bg-gray-100 text-gray-500')]">
                  {{ user.status === 'active' ? t('admin_active') : t('admin_inactive') }}
                </span>
              </div>
            </div>
          </div>

          <div :class="['grid grid-cols-1 md:grid-cols-2 gap-4 mb-8', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_joined') }}</span>
              <span>{{ formatToShort(user.joined) }}</span>
            </div>
            <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_role') }}</span>
              <span>{{ getRoleLabel(user.roleId) }}</span>
            </div>
          </div>

          <div class="flex gap-4">
            <button 
              @click="goBack" 
              :class="['flex-1 py-3 font-bold tracking-wider rounded-lg transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-900']"
            >
              {{ t('admin_close') }}
            </button>
            <button 
              @click="handleEdit(user)" 
              class="flex-1 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider rounded-lg transition-colors"
            >
              {{ t('admin_edit') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else :class="['text-center py-16', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
      <User :size="64" class="mx-auto mb-4 opacity-50" />
      <p class="text-xl">{{ t('no_artifacts') || '用户不存在' }}</p>
      <button 
        @click="goBack" 
        :class="['mt-6 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider rounded-lg transition-colors']"
      >
        {{ t('admin_users') }}
      </button>
    </div>

    <UserForm
      :edit-data="editingUser"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />
  </div>
</template>
