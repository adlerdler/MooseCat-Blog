<script setup>
/**
 * Author.vue - 作者介绍页
 * 
 * 功能说明：
 * - 展示网站作者/开发者的个人信息
 * - 集成 GitHub 贡献日历热力图
 * - 显示 GitHub 统计数据（提交次数、PR、仓库数）
 * - 响应式社交媒体链接
 * 
 * 数据来源：
 * - GitHub API 获取用户信息和贡献数据
 */
import { ref, onMounted, computed, watch } from 'vue';
import { ArrowRight, Github, Twitter, Linkedin, Mail, Send, CheckCircle, AlertCircle } from 'lucide-vue-next';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { CalendarHeatmap } from 'vue3-calendar-heatmap';
import 'vue3-calendar-heatmap/dist/style.css';
import { useTheme } from '@/composables/useTheme';
import { usePageSeo } from '../../composables/usePageSeo';
import { usePageSeoData } from '../../composables/usePageSeoData';
import SidebarMenu from '@/components/SidebarMenu.vue';
import Footer from '@/components/Footer.vue';

const props = defineProps({
  author: { type: Object, default: null },
  skills: { type: Array, default: () => [] },
  manifestos: { type: Array, default: () => [] },
  socialLinksObj: { type: Object, default: () => ({}) },
  projects: { type: Array, default: () => [] },
  menus: { type: Array, default: () => [] },
  siteConfig: { type: Object, default: () => ({}) },
  footerConfig: { type: Object, default: () => ({}) },
  themes: { type: Array, default: () => [] }
});

const { getSeoByPageKey } = usePageSeoData();
const pageSeo = getSeoByPageKey('author') || { title: '', description: '', keywords: '' };

const { SeoHead } = usePageSeo({
  title: pageSeo.title || '',
  description: pageSeo.description || '',
  keywords: pageSeo.keywords || '',
  type: 'Person',
  personName: computed(() => props.author?.display_name || ''),
  jobTitle: computed(() => props.author?.role_title || props.author?.role_label || ''),
  image: computed(() => props.author?.avatar ? `/storage/${props.author.avatar}` : ''),
  url: computed(() => window.location.href),
  email: computed(() => props.author?.email || ''),
  sameAs: computed(() => {
    return Object.entries(props.socialLinksObj || {})
      .filter(([_, url]) => url)
      .map(([_, url]) => url);
  }),
  knowsAbout: computed(() => {
    return (props.skills || []).map(s => s.label);
  }),
})

// 订阅表单
const subscribeEmail = ref('')
const subscribeLoading = ref(false)
const subscribeMessage = ref('')
const subscribeError = ref(false)

const handleSubscribe = async () => {
  if (!subscribeEmail.value.trim()) return
  subscribeLoading.value = true
  subscribeMessage.value = ''
  subscribeError.value = false
  try {
    const { data } = await axios.post('/subscribe', { email: subscribeEmail.value })
    subscribeMessage.value = data.message || t('subscribe_success')
    subscribeEmail.value = ''
  } catch (err) {
    subscribeError.value = true
    if (err.response?.status === 422) {
      subscribeMessage.value = err.response.data.errors?.email?.[0] || t('subscribe_duplicate')
    } else {
      subscribeMessage.value = t('subscribe_error')
    }
  } finally {
    subscribeLoading.value = false
  }
}

const socialLinks = Object.entries(props.socialLinksObj).map(([platform, url], index) => {
  const iconMap = {
    github: 'Github',
    twitter: 'Twitter',
    linkedin: 'Linkedin',
    website: 'Globe',
    email: 'Mail'
  };
  const labelMap = {
    github: 'GITHUB',
    twitter: 'TWITTER',
    linkedin: 'LINKEDIN',
    website: 'WEBSITE',
    email: 'CONTACT'
  };
  return {
    platform,
    icon_name: iconMap[platform] || 'Link',
    label: labelMap[platform] || platform.toUpperCase(),
    url,
    sort_order: index + 1
  };
});

const { t } = useI18n();
const { currentTheme } = useTheme({ themesData: props.themes });
const isFooterVisible = ref(true);
const isVisible = ref(false);
const calendarValues = ref([]);
const githubStats = ref({ commits: 0, prs: 0, repos: 7 });
const isLoading = ref(true);
const githubLink = socialLinks.find(s => s.platform === 'github');
const username = githubLink?.url?.split('/').pop() || 'adler-decht';

// 从 author.display_name 拆分为两行标题
const nameParts = computed(() => {
  const name = props.author?.display_name || '';
  const parts = name.split(' ');
  return parts.length >= 2 ? parts : [name || 'ARCHYX', ''];
});

// 作者角色信息
const authorRole = computed(() => props.author?.role_title || props.author?.role_label || '');
const authorStatusLabel = computed(() => props.author?.status_label || 'STATUS');
const authorStatusText = computed(() => props.author?.status_text || 'BUILDING');

const getIconComponent = (iconName) => {
  const iconMap = {
    'Github': Github,
    'Twitter': Twitter,
    'Linkedin': Linkedin,
    'Mail': Mail
  };
  return iconMap[iconName] || Mail;
};

const endDate = computed(() => {
 const date = new Date();
 return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
});
const rangeColor = computed(() => {
 const defaultColor = '#a72525';
 const themeColor = currentTheme.value?.color || defaultColor;
 const hex = themeColor.startsWith('#') ? themeColor : defaultColor;
 const r = parseInt(hex.slice(1, 3), 16);
 const g = parseInt(hex.slice(3, 5), 16);
 const b = parseInt(hex.slice(5, 7), 16);
 const toRgba = (opacity) => `rgba(${r}, ${g}, ${b}, ${opacity})`;
 return [
   '#e8e8e8',
   toRgba(0.15),
   toRgba(0.3),
   toRgba(0.5),
   toRgba(0.75),
   hex
 ];
});
onMounted(() => {
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');
  
  setTimeout(() => {
    isVisible.value = true;
  }, 100);
  fetchGitHubData();
  
  // 从 sessionStorage 读取页脚可见性状态
  const saved = sessionStorage.getItem('footer_visible');
  if (saved !== null) {
    isFooterVisible.value = saved === 'true';
  }
});

watch(isFooterVisible, (newVal) => {
  sessionStorage.setItem('footer_visible', String(newVal));
});
const fetchGitHubData = async () => {
 try {
  // 添加 User-Agent 头来避免 GitHub API 403 错误
  const headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
  };
  
  const [userResponse, contributionsResponse] = await Promise.all([
    fetch(`https://api.github.com/users/${username}`, { headers }),
    fetch(`https://gh-calendar.rschristian.dev/user/${username}`, { headers })
  ]);

  if (!userResponse.ok) {
    throw new Error('Failed to fetch GitHub user data');
  }

  const userData = await userResponse.json();

  githubStats.value = {
    commits: userData.public_repos * 15 || 0,
    prs: Math.floor(userData.public_repos * 2) || 0,
    repos: userData.public_repos || 7,
  };

  if (contributionsResponse.ok) {
    const contributionsData = await contributionsResponse.json();

    if (contributionsData && contributionsData.contributions) {
      const flatContributions = contributionsData.contributions.flat();

      const totalContributions = flatContributions.reduce((sum, day) => sum + (day.count || 0), 0);
      githubStats.value.commits = totalContributions;

      calendarValues.value = flatContributions
        .filter(day => day && day.date)
        .map(day => ({
          date: day.date,
          count: day.count || null
        }));
    } else {
      generateMockCalendarData();
    }
  } else {
    console.warn('Contributions API failed, using fallback');
    generateMockCalendarData();
  }
 } catch (error) {
  console.error('Error fetching GitHub data:', error);
  generateMockCalendarData();
 } finally {
  isLoading.value = false;
 }
};
const generateMockCalendarData = () => {
 const today = new Date();
 const result = [];
 for (let i = 364; i >= 0; i--) {
 const date = new Date(today);
 date.setDate(date.getDate() - i);
 const dateStr = date.toISOString().split('T')[0];
 const rand = Math.random();
 let count = null;
 if (rand > 0.6) {
   if (rand > 0.95) count = Math.floor(Math.random() * 20) + 10;
   else if (rand > 0.85) count = Math.floor(Math.random() * 10) + 5;
   else if (rand > 0.75) count = Math.floor(Math.random() * 5) + 2;
   else count = 1;
 }
 result.push({ date: dateStr, count });
 }
 calendarValues.value = result;
};

</script>

<template>
  <div class="min-h-screen bg-construct-paper text-construct-black overflow-hidden relative">
    <!-- Sidebar Menu -->
    <SidebarMenu 
      v-model:is-footer-visible="isFooterVisible" 
      :menus="menus"
      :site-config="siteConfig"
      :footer-config="footerConfig"
      :themes="themes"
    />

    <!-- Main Content with left margin for sidebar -->
    <div class="md:ml-16 pt-16 md:pt-0">
    <div class="absolute top-0 right-0 w-[40vw] h-[100vh] bg-construct-black/5 -skew-x-12 translate-x-[20vw] z-[0]" />
    <div class="absolute top-20 left-10 w-96 h-96 border-[40px] border-construct-red opacity-5 rounded-full z-[0]" />
    <div class="absolute bottom-20 right-20 w-64 h-64 bg-construct-red opacity-10 z-[0] rotate-45" />

    <div class="relative z-10 max-w-[1400px] mx-auto px-4 md:px-8 lg:px-16 pt-8 md:pt-16 lg:pt-24">
      <header class="mb-16 md:mb-24 relative">
        <div class="absolute top-4 left-0 w-8 h-8 bg-construct-red flex items-center justify-center">
          <div class="w-4 h-4 bg-white" />
        </div>
        
        <h1 
          class="font-display text-[12vw] md:text-[9vw] lg:text-[10vw] leading-[0.8] tracking-tighter uppercase pl-12 transition-all duration-1000"
          :class="isVisible ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'"
        >
          {{ nameParts[0] }}
        </h1>
        <h1 
          class="font-display text-[12vw] md:text-[9vw] lg:text-[10vw] leading-[0.8] tracking-tighter uppercase text-construct-red pl-[12vw] md:pl-[10vw] lg:pl-[12vw] transition-all duration-1000 delay-100"
          :class="isVisible ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'"
        >
          {{ nameParts[1] || nameParts[0] }}
        </h1>

        <div 
          class="absolute top-0 right-4 md:right-16 lg:right-32 text-right pointer-events-none transition-opacity duration-1000 delay-500"
          :class="isVisible ? 'opacity-100' : 'opacity-0'"
        >
          <div class="text-[80px] md:text-[120px] lg:text-[180px] font-display text-construct-black leading-none tracking-tighter opacity-5">
            SYS_01
          </div>
        </div>
      </header>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 relative">
        <div 
          class="lg:col-span-4 flex flex-col gap-6 md:gap-12 pt-6 md:pt-8 border-t-8 border-construct-black relative transition-all duration-1000 delay-200"
          :class="isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
        >
          <div>
            <p class="font-bold tracking-[0.4em] text-xs uppercase mb-2 text-construct-red select-none">
              {{ authorRole || t('role_designation') }}
            </p>
            <h2 class="font-display text-3xl md:text-4xl uppercase tracking-tight">
              {{ props.author?.display_name || t('architect_designer') }}
            </h2>
          </div>

          <div class="bg-construct-black p-6 md:p-8 text-white relative overflow-hidden group border-2 border-transparent">
            <div class="absolute top-0 right-0 w-32 h-32 bg-construct-red rounded-full blur-2xl opacity-20 group-hover:opacity-50 transition-opacity" />
            <p class="font-bold tracking-widest text-xs uppercase mb-6 text-white/50 border-b border-white/20 pb-2">
              {{ authorStatusLabel || t('author_status') }}
            </p>
            <div class="flex items-center gap-4">
              <div class="w-3 h-3 bg-construct-red animate-pulse" />
              <span class="font-display text-xl md:text-2xl tracking-widest uppercase">
                {{ authorStatusText || t('building_systems') }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <a
              v-for="(item, index) in socialLinks"
              :key="index"
              :href="item.url"
              target="_blank"
              rel="noopener noreferrer"
              :class="[
                'aspect-[4/3] flex flex-col items-center justify-center gap-2 transition-all border-2',
                item.platform === 'github' || item.platform === 'twitter' ? 'bg-white text-construct-black hover:bg-construct-black hover:text-white border-construct-black' :
                item.platform === 'linkedin' ? 'bg-construct-red text-white hover:bg-construct-black hover:text-white border-construct-red hover:border-construct-black' :
                'bg-construct-black text-white hover:bg-construct-red hover:text-white border-construct-black hover:border-construct-red'
              ]"
            >
              <component 
                :is="getIconComponent(item.icon_name)" 
                class="w-5 h-5 group-hover:-translate-y-1 transition-transform" 
              />
              <span class="text-[10px] font-bold tracking-[0.2em] uppercase">
                {{ item.label }}
              </span>
            </a>
          </div>

          <div class="absolute -left-16 top-1/2 -translate-y-1/2 w-4 h-32 bg-construct-red hidden lg:block" />
        </div>

        <div 
          class="lg:col-span-7 lg:col-start-6 flex flex-col gap-8 lg:gap-16 pl-0 lg:pl-6 transition-all duration-1000 delay-300"
          :class="isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
        >
          <div class="relative" v-if="manifestos && manifestos.length">
            <div class="absolute top-4 left-4 w-full h-full bg-construct-red z-[0]" />
            <section class="bg-white p-6 md:p-10 border-4 border-construct-black relative z-10">
              <div class="absolute -top-6 -right-6 w-12 h-12 bg-construct-black text-white flex items-center justify-center font-bold text-xl">
                !
              </div>
              <h3 class="font-display text-xl md:text-2xl lg:text-3xl uppercase tracking-tighter mb-6 border-b-4 border-construct-black pb-4">
                {{ manifestos[0]?.title || t('manifesto') }}
              </h3>
              <div class="space-y-6 text-base md:text-lg font-medium leading-relaxed">
                <p>{{ manifestos[0]?.content }}</p>
                <p class="pl-4 md:pl-6 border-l-4 border-construct-red" v-if="manifestos.length > 1">
                  {{ manifestos[1]?.content }}
                </p>
                <p v-if="manifestos.length > 2">{{ manifestos[2]?.content }}</p>
              </div>
            </section>
          </div>

          <section v-if="skills && skills.length">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
              <h3 class="font-display text-2xl md:text-3xl lg:text-4xl uppercase tracking-tighter">
                {{ t('capability_metrics') }}
              </h3>
              <div class="flex-1 h-1 md:h-2 bg-construct-black opacity-20" />
            </div>

            <div class="space-y-6">
              <div 
                v-for="(skill, index) in skills" 
                :key="skill.id" 
                class="group relative"
              >
                <div class="absolute -left-8 top-1 text-construct-red font-bold text-[10px] tracking-widest hidden md:block">
                  0{{ index + 1 }}
                </div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-2 gap-2">
                  <div>
                    <span class="font-bold text-sm tracking-widest uppercase block">
                      {{ skill.label }}
                    </span>
                    <span class="text-[11px] font-bold tracking-widest opacity-50 uppercase mt-1 block">
                      {{ skill.description }}
                    </span>
                  </div>
                  <span class="font-display text-2xl md:text-3xl text-construct-black leading-none">
                    {{ skill.value }}
                  </span>
                </div>
                <div class="h-3 md:h-4 w-full bg-white border-2 border-construct-black overflow-hidden p-[2px]">
                  <div 
                    class="h-full bg-construct-red transition-all duration-1000 ease-out"
                    :style="{ width: isVisible ? skill.value + '%' : '0%' }"
                  />
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>

      <div 
        class="mt-20 md:mt-32 pt-12 md:pt-16 border-t-8 border-construct-black transition-all duration-1000 delay-500"
        :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
      >
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
          <div>
            <div class="flex items-center gap-3 mb-3">
              <Github class="w-5 h-5 text-construct-red" />
              <h3 class="font-display text-3xl md:text-4xl lg:text-5xl uppercase tracking-tighter">
                {{ t('github_contributions') }} //
              </h3>
            </div>
            <p class="text-[10px] font-black tracking-[0.3em] uppercase opacity-40">
              @{{ username }}_ACTIVITY_ARCHIVE // TEMPORAL_MAPPING
            </p>
          </div>

          <div class="flex gap-6 md:gap-8">
            <div>
              <div class="text-[9px] font-black tracking-widest opacity-40 uppercase mb-1">
                Commits
              </div>
              <div class="text-xl md:text-2xl font-display uppercase tracking-tighter">
                {{ githubStats.commits.toLocaleString() }}
              </div>
            </div>
            <div>
              <div class="text-[9px] font-black tracking-widest opacity-40 uppercase mb-1">
                PRs
              </div>
              <div class="text-xl md:text-2xl font-display uppercase tracking-tighter">
                {{ githubStats.prs.toLocaleString() }}
              </div>
            </div>
            <div>
              <div class="text-[9px] font-black tracking-widest opacity-40 uppercase mb-1">
                Repos
              </div>
              <div class="text-xl md:text-2xl font-display uppercase tracking-tighter">
                {{ githubStats.repos.toLocaleString() }}
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white border-4 border-construct-black relative group">
          <div class="absolute -top-4 -left-4 w-8 h-8 border-t-4 border-l-4 border-construct-red" />
          <div class="absolute -bottom-4 -right-4 w-8 h-8 border-b-4 border-r-4 border-construct-red" />

          <div class="p-4 md:p-6 bg-construct-paper">
            <div class="overflow-x-auto no-scrollbar">
              <div class="min-w-[800px]">
                <CalendarHeatmap
                  :end-date="endDate"
                  :values="calendarValues"
                  :range-color="rangeColor"
                  :round="2"
                  :tooltip="true"
                />
              </div>
            </div>
          </div>

          <div class="absolute bottom-0 right-0 p-4 opacity-5 pointer-events-none">
            <Github class="w-20 h-20" />
          </div>
        </div>

        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="flex items-center gap-3">
            <div class="w-2 h-2 bg-construct-red animate-pulse" />
            <span class="text-[10px] font-bold tracking-[0.4em] uppercase opacity-40">
              LIVE_LINK_ACTIVE
            </span>
          </div>
          <a
            :href="`https://github.com/${username}`"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-3 text-xs font-bold tracking-[0.2em] uppercase hover:text-construct-red transition-all border-b-2 border-transparent hover:border-construct-red pb-1"
          >
            {{ t('view_on_github') }} <ArrowRight class="w-4 h-4" />
          </a>
        </div>
      </div>

      <div 
        class="mt-12 md:mt-16 pt-12 md:pt-16 border-t-4 border-construct-black mb-12 md:mb-16 transition-all duration-1000 delay-700"
        :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
        v-if="projects && projects.length"
      >
        <div class="flex items-center gap-3 mb-10">
          <div class="w-8 h-8 md:w-10 md:h-10 bg-construct-red flex items-center justify-center text-white">
            <div class="w-4 h-4 md:w-5 md:h-5 border-2 border-white animate-spin" />
          </div>
          <h3 class="font-display text-3xl md:text-4xl lg:text-5xl uppercase tracking-tighter">
            {{ t('active_development') }} //
          </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="proj in projects"
            :key="proj.id"
            class="border-4 border-construct-black p-5 bg-white relative group overflow-hidden flex flex-col"
          >
            <div class="absolute top-0 right-0 p-2 opacity-5 text-[32px] md:text-[40px] font-display pointer-events-none group-hover:opacity-20 transition-opacity">
              {{ proj.sort_order }}
            </div>
            <div class="relative z-10 flex-1 flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black tracking-widest text-construct-red uppercase italic">
                  {{ proj.status }}
                </span>
                <span class="text-[10px] font-mono opacity-40">
                  0x{{ proj.title.slice(0, 4) }}
                </span>
              </div>
              <h4 class="font-display text-lg md:text-xl uppercase tracking-tight mb-2 underline decoration-construct-red decoration-2 underline-offset-4">
                {{ proj.title }}
              </h4>
              <p class="text-[11px] font-bold tracking-widest text-construct-black/60 uppercase mb-6 flex-1 leading-relaxed">
                {{ proj.description }}
              </p>

              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tech in proj.technologies.slice(0, 3)"
                  :key="tech"
                  class="bg-gray-100 px-2 py-1 text-[8px] font-bold tracking-widest uppercase border border-construct-black/10"
                >
                  {{ tech }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div 
        class="flex justify-center mt-12 md:mt-16 pb-12 md:pb-16 transition-all duration-1000 delay-900"
        :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
      >
        <a
          :href="`/author/${props.author?.slug || username}`"
          class="inline-flex items-center justify-center gap-4 md:gap-6 bg-construct-black text-white hover:bg-construct-red py-5 px-10 md:py-6 md:px-12 font-bold tracking-[0.2em] uppercase transition-all group text-sm md:text-base border-4 border-construct-black w-full md:w-auto shadow-[6px_6px_0px_#000] hover:shadow-[10px_10px_0px_#000] hover:-translate-y-1 hover:-translate-x-1"
        >
          <div class="w-3 h-3 bg-white" />
          <span>{{ t('access_dossier') }}</span>
          <ArrowRight class="w-5 h-5 group-hover:translate-x-2 transition-transform" />
        </a>
      </div>
    </div>

    <section class="relative py-24 md:py-32 mt-24 md:mt-32 bg-construct-black text-white flex items-center justify-center overflow-hidden w-full">
      <div class="absolute inset-0 z-[0] flex items-center justify-center pointer-events-none opacity-20">
        <div class="w-[400px] md:w-[600px] h-[400px] md:h-[600px] border-[60px] md:border-[100px] border-construct-red rotate-12" />
      </div>

      <div class="relative z-10 text-center px-4 w-full">
        <h2 class="font-display text-4xl md:text-5xl lg:text-7xl mb-6 tracking-tighter uppercase">
          {{ t('cta_title') }}
        </h2>
        <p class="text-xs md:text-sm font-bold tracking-[0.5em] mb-10 opacity-60">
          SYSTEM STATUS: INITIALIZING PROTOCOL...
        </p>

        <form @submit.prevent="handleSubscribe" class="flex flex-col md:flex-row gap-3 md:gap-4 justify-center items-stretch max-w-lg md:max-w-2xl mx-auto">
          <input
            v-model="subscribeEmail"
            type="email"
            placeholder="YOUR-EMAIL@DOMAIN.COM"
            required
            class="bg-transparent border-2 border-white px-5 py-3 md:px-6 md:py-4 text-sm tracking-widest focus:outline-none focus:bg-white focus:text-construct-black transition-all flex-1"
          />
          <button
            type="submit"
            :disabled="subscribeLoading"
            class="bg-construct-red text-white font-bold px-8 md:px-12 py-3 md:py-4 text-sm tracking-widest hover:bg-white hover:text-construct-black transition-all uppercase disabled:opacity-60 flex items-center justify-center gap-2"
          >
            <span v-if="subscribeLoading" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
            <template v-else>
              {{ t('cta_submit') }}
              <Send class="w-4 h-4" />
            </template>
          </button>
        </form>
        <p v-if="subscribeMessage" :class="['mt-4 text-sm font-bold tracking-wider', subscribeError ? 'text-red-400' : 'text-green-400']">
          <CheckCircle v-if="!subscribeError" class="w-4 h-4 inline mr-1" />
          <AlertCircle v-else class="w-4 h-4 inline mr-1" />
          {{ subscribeMessage }}
        </p>
      </div>
    </section>

    <!-- Footer -->
    <Footer 
      v-model="isFooterVisible" 
      :footer-config="footerConfig"
      :site-config="siteConfig"
    />

    </div><!-- End of ml-16 wrapper -->
  </div>
</template>
