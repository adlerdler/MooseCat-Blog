<script setup>
/**
 * Admin.vue - 管理后台首页
 * 
 * 功能说明：
 * - 网站内容管理后台的首页/控制面板
 * - 展示关键统计数据（文章数、视频数、项目数、访客数）
 * - 管理菜单导航到各个内容管理模块
 * 
 * 功能模块：
 * - Dashboard - 系统概览和数据分析
 * - Posts - 文章管理（增删改查）
 * - Videos - 视频管理
 * - Projects - 项目管理
 * - Resources - 资源管理
 * - Users - 用户管理
 * - Analytics - 数据分析
 * - Settings - 系统设置
 * 
 * 技术特点：
 * - 深色主题管理界面
 * - 响应式布局
 * - 活动日志记录
 */
import { ref, computed } from 'vue';
import { 
  LayoutDashboard, 
  FileText, 
  Play, 
  FolderKanban, 
  BookOpen, 
  Users, 
  Settings,
  BarChart3,
  Activity,
  TrendingUp,
  Clock,
  Eye,
  Edit3,
  Trash2,
  Plus
} from 'lucide-vue-next';
import { POSTS } from '../../data/posts';
import { VIDEOS } from '../../data/videos';
import { PROJECTS } from '../../data/projects';

const activeSection = ref('dashboard');

const stats = computed(() => [
  { 
    label: 'POSTS', 
    value: POSTS.length, 
    change: '+12%',
    icon: FileText,
    color: 'text-construct-red'
  },
  { 
    label: 'VIDEOS', 
    value: VIDEOS.length, 
    change: '+8%',
    icon: Play,
    color: 'text-blue-500'
  },
  { 
    label: 'PROJECTS', 
    value: PROJECTS.length, 
    change: '+15%',
    icon: FolderKanban,
    color: 'text-green-500'
  },
  { 
    label: 'VISITORS', 
    value: '2.4K', 
    change: '+23%',
    icon: Eye,
    color: 'text-purple-500'
  }
]);

const recentPosts = computed(() => POSTS.slice(0, 5));
const recentVideos = computed(() => VIDEOS.slice(0, 5));

const menuItems = [
  { id: 'dashboard', label: 'DASHBOARD', icon: LayoutDashboard },
  { id: 'posts', label: 'POSTS', icon: FileText },
  { id: 'videos', label: 'VIDEOS', icon: Play },
  { id: 'projects', label: 'PROJECTS', icon: FolderKanban },
  { id: 'resources', label: 'RESOURCES', icon: BookOpen },
  { id: 'users', label: 'USERS', icon: Users },
  { id: 'analytics', label: 'ANALYTICS', icon: BarChart3 },
  { id: 'settings', label: 'SETTINGS', icon: Settings },
];

const activityLog = [
  { action: 'New post published', target: 'THE GEOMETRY OF PERCEPTION', time: '2 hours ago', type: 'post' },
  { action: 'Video uploaded', target: 'Constructivist Design Principles', time: '5 hours ago', type: 'video' },
  { action: 'Project updated', target: 'Digital Archive System', time: '1 day ago', type: 'project' },
  { action: 'New comment approved', target: 'By Anonymous', time: '1 day ago', type: 'comment' },
  { action: 'User registered', target: 'New Member', time: '2 days ago', type: 'user' },
];
</script>

<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-construct-red flex items-center justify-center">
            <LayoutDashboard size="24" />
          </div>
          <h1 class="font-display text-2xl tracking-tighter uppercase">ARCHYX // ADMIN</h1>
        </div>
        <div class="flex items-center gap-6">
          <div class="relative">
            <Activity size="20" class="text-gray-400" />
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
          </div>
          <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center">
            <Users size="18" />
          </div>
        </div>
      </div>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 bg-gray-800 border-r border-gray-700 min-h-[calc(100vh-65px)]">
        <nav class="p-4 space-y-1">
          <button
            v-for="item in menuItems"
            :key="item.id"
            @click="activeSection = item.id"
            class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold tracking-widest uppercase transition-all"
            :class="activeSection === item.id 
              ? 'bg-construct-red text-white' 
              : 'text-gray-400 hover:bg-gray-700 hover:text-white'"
          >
            <component :is="item.icon" size="18" />
            {{ item.label }}
          </button>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 p-8">
        <!-- Dashboard Section -->
        <div v-if="activeSection === 'dashboard'">
          <!-- Page Header -->
          <div class="mb-8">
            <h2 class="font-display text-4xl tracking-tighter mb-2">CONTROL PANEL</h2>
            <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">System Overview & Analytics</p>
          </div>

          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div 
              v-for="stat in stats" 
              :key="stat.label"
              class="bg-gray-800 border border-gray-700 p-6 hover:border-construct-red transition-colors"
            >
              <div class="flex items-center justify-between mb-4">
                <component :is="stat.icon" :class="stat.color" size="24" />
                <span class="text-xs font-bold text-green-400">{{ stat.change }}</span>
              </div>
              <div class="font-display text-4xl mb-1">{{ stat.value }}</div>
              <div class="text-xs font-bold tracking-widest uppercase text-gray-400">{{ stat.label }}</div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Posts -->
            <div class="lg:col-span-2 bg-gray-800 border border-gray-700 p-6">
              <div class="flex items-center justify-between mb-6">
                <h3 class="font-display text-xl tracking-tighter">RECENT POSTS</h3>
                <button class="flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-construct-red hover:underline">
                  <Plus size="14" /> ADD NEW
                </button>
              </div>
              <div class="space-y-4">
                <div 
                  v-for="post in recentPosts" 
                  :key="post.id"
                  class="flex items-center justify-between p-4 bg-gray-700/50 hover:bg-gray-700 transition-colors"
                >
                  <div class="flex-1">
                    <h4 class="font-display text-lg tracking-tighter mb-1">{{ post.title }}</h4>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                      <span>{{ post.category }}</span>
                      <span>{{ post.date }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <button class="p-2 hover:bg-gray-600 transition-colors">
                      <Edit3 size="16" />
                    </button>
                    <button class="p-2 hover:bg-red-500/20 transition-colors">
                      <Trash2 size="16" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-gray-800 border border-gray-700 p-6">
              <h3 class="font-display text-xl tracking-tighter mb-6">ACTIVITY LOG</h3>
              <div class="space-y-4">
                <div 
                  v-for="(activity, index) in activityLog" 
                  :key="index"
                  class="flex items-start gap-3"
                >
                  <div class="w-2 h-2 rounded-full mt-2" :class="{
                    'bg-construct-red': activity.type === 'post',
                    'bg-blue-500': activity.type === 'video',
                    'bg-green-500': activity.type === 'project',
                    'bg-yellow-500': activity.type === 'comment',
                    'bg-purple-500': activity.type === 'user'
                  }"></div>
                  <div>
                    <p class="text-sm font-medium">{{ activity.action }}</p>
                    <p class="text-xs text-gray-400">{{ activity.target }} - {{ activity.time }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Analytics Chart Placeholder -->
          <div class="mt-6 bg-gray-800 border border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="font-display text-xl tracking-tighter">TRAFFIC ANALYTICS</h3>
              <div class="flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-gray-400">
                <TrendingUp size="16" class="text-green-400" /> +23%
              </div>
            </div>
            <div class="h-48 flex items-end gap-2">
              <div class="flex-1 bg-gray-700 hover:bg-construct-red transition-colors" style="height: 60%"></div>
              <div class="flex-1 bg-gray-700 hover:bg-construct-red transition-colors" style="height: 80%"></div>
              <div class="flex-1 bg-gray-700 hover:bg-construct-red transition-colors" style="height: 45%"></div>
              <div class="flex-1 bg-gray-700 hover:bg-construct-red transition-colors" style="height: 90%"></div>
              <div class="flex-1 bg-gray-700 hover:bg-construct-red transition-colors" style="height: 70%"></div>
              <div class="flex-1 bg-gray-700 hover:bg-construct-red transition-colors" style="height: 85%"></div>
              <div class="flex-1 bg-construct-red" style="height: 95%"></div>
            </div>
            <div class="flex justify-between mt-4 text-xs text-gray-400">
              <span>MON</span>
              <span>TUE</span>
              <span>WED</span>
              <span>THU</span>
              <span>FRI</span>
              <span>SAT</span>
              <span>SUN</span>
            </div>
          </div>
        </div>

        <!-- Posts Section -->
        <div v-if="activeSection === 'posts'">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="font-display text-4xl tracking-tighter mb-2">MANAGE POSTS</h2>
              <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Content Management</p>
            </div>
            <button class="flex items-center gap-2 bg-construct-red px-6 py-3 text-sm font-bold tracking-widest uppercase hover:bg-red-600 transition-colors">
              <Plus size="16" /> ADD POST
            </button>
          </div>
          
          <div class="bg-gray-800 border border-gray-700 overflow-hidden">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-700">
                  <th class="px-6 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">TITLE</th>
                  <th class="px-6 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">CATEGORY</th>
                  <th class="px-6 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">DATE</th>
                  <th class="px-6 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">AUTHOR</th>
                  <th class="px-6 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="post in POSTS" 
                  :key="post.id"
                  class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors"
                >
                  <td class="px-6 py-4 text-sm font-mono">{{ post.id.padStart(2, '0') }}</td>
                  <td class="px-6 py-4">
                    <h4 class="font-display text-lg tracking-tighter">{{ post.title }}</h4>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-gray-700 text-xs font-bold tracking-widest uppercase">{{ post.category }}</span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-400">{{ post.date }}</td>
                  <td class="px-6 py-4 text-sm">{{ post.author }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <button class="p-2 hover:bg-gray-600 transition-colors">
                        <Edit3 size="16" />
                      </button>
                      <button class="p-2 hover:bg-red-500/20 transition-colors">
                        <Trash2 size="16" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Videos Section -->
        <div v-if="activeSection === 'videos'">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="font-display text-4xl tracking-tighter mb-2">MANAGE VIDEOS</h2>
              <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Video Library</p>
            </div>
            <button class="flex items-center gap-2 bg-construct-red px-6 py-3 text-sm font-bold tracking-widest uppercase hover:bg-red-600 transition-colors">
              <Plus size="16" /> UPLOAD VIDEO
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="video in VIDEOS" 
              :key="video.id"
              class="bg-gray-800 border border-gray-700 overflow-hidden hover:border-construct-red transition-colors"
            >
              <div class="aspect-video bg-gray-700 relative">
                <div class="absolute inset-0 flex items-center justify-center">
                  <Play size="48" class="text-construct-red" />
                </div>
              </div>
              <div class="p-4">
                <h4 class="font-display text-lg tracking-tighter mb-2">{{ video.title }}</h4>
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-400">{{ video.duration }}</span>
                  <div class="flex items-center gap-2">
                    <button class="p-2 hover:bg-gray-600 transition-colors">
                      <Edit3 size="16" />
                    </button>
                    <button class="p-2 hover:bg-red-500/20 transition-colors">
                      <Trash2 size="16" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Projects Section -->
        <div v-if="activeSection === 'projects'">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="font-display text-4xl tracking-tighter mb-2">MANAGE PROJECTS</h2>
              <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Project Portfolio</p>
            </div>
            <button class="flex items-center gap-2 bg-construct-red px-6 py-3 text-sm font-bold tracking-widest uppercase hover:bg-red-600 transition-colors">
              <Plus size="16" /> NEW PROJECT
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div 
              v-for="project in PROJECTS" 
              :key="project.id"
              class="bg-gray-800 border border-gray-700 p-6 hover:border-construct-red transition-colors"
            >
              <div class="flex items-start justify-between mb-4">
                <div>
                  <span class="text-xs font-bold tracking-widest uppercase text-construct-red">{{ project.status }}</span>
                  <h4 class="font-display text-2xl tracking-tighter mt-2">{{ project.title }}</h4>
                </div>
                <div class="flex items-center gap-2">
                  <button class="p-2 hover:bg-gray-600 transition-colors">
                    <Edit3 size="16" />
                  </button>
                  <button class="p-2 hover:bg-red-500/20 transition-colors">
                    <Trash2 size="16" />
                  </button>
                </div>
              </div>
              <p class="text-gray-400 mb-4">{{ project.excerpt }}</p>
              <div class="flex items-center gap-4 text-xs">
                <span>{{ project.tech }}</span>
                <span class="text-gray-500">|</span>
                <span>{{ project.year }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Resources Section -->
        <div v-if="activeSection === 'resources'">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="font-display text-4xl tracking-tighter mb-2">RESOURCE LIBRARY</h2>
              <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Downloadable Assets</p>
            </div>
            <button class="flex items-center gap-2 bg-construct-red px-6 py-3 text-sm font-bold tracking-widest uppercase hover:bg-red-600 transition-colors">
              <Plus size="16" /> ADD RESOURCE
            </button>
          </div>
          
          <div class="bg-gray-800 border border-gray-700 p-8 text-center">
            <BookOpen size="64" class="mx-auto text-gray-600 mb-4" />
            <p class="font-display text-xl tracking-tighter text-gray-400">RESOURCES SECTION</p>
            <p class="text-sm text-gray-500 mt-2">Upload and manage downloadable resources</p>
          </div>
        </div>

        <!-- Users Section -->
        <div v-if="activeSection === 'users'">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="font-display text-4xl tracking-tighter mb-2">USER MANAGEMENT</h2>
              <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Registered Members</p>
            </div>
            <button class="flex items-center gap-2 bg-construct-red px-6 py-3 text-sm font-bold tracking-widest uppercase hover:bg-red-600 transition-colors">
              <Plus size="16" /> ADD USER
            </button>
          </div>
          
          <div class="bg-gray-800 border border-gray-700 p-8 text-center">
            <Users size="64" class="mx-auto text-gray-600 mb-4" />
            <p class="font-display text-xl tracking-tighter text-gray-400">USER MANAGEMENT</p>
            <p class="text-sm text-gray-500 mt-2">Manage registered users and permissions</p>
          </div>
        </div>

        <!-- Analytics Section -->
        <div v-if="activeSection === 'analytics'">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="font-display text-4xl tracking-tighter mb-2">ANALYTICS</h2>
              <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Performance Metrics</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold tracking-widest uppercase">
              <Clock size="16" class="text-gray-400" />
              LAST 30 DAYS
            </div>
          </div>
          
          <div class="bg-gray-800 border border-gray-700 p-8 text-center">
            <BarChart3 size="64" class="mx-auto text-gray-600 mb-4" />
            <p class="font-display text-xl tracking-tighter text-gray-400">ANALYTICS DASHBOARD</p>
            <p class="text-sm text-gray-500 mt-2">View detailed performance metrics and insights</p>
          </div>
        </div>

        <!-- Settings Section -->
        <div v-if="activeSection === 'settings'">
          <div class="mb-8">
            <h2 class="font-display text-4xl tracking-tighter mb-2">SYSTEM SETTINGS</h2>
            <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Configuration</p>
          </div>
          
          <div class="bg-gray-800 border border-gray-700 p-8 text-center">
            <Settings size="64" class="mx-auto text-gray-600 mb-4" />
            <p class="font-display text-xl tracking-tighter text-gray-400">SYSTEM SETTINGS</p>
            <p class="text-sm text-gray-500 mt-2">Configure system preferences and options</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>
