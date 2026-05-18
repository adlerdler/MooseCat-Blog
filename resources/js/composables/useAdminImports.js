/**
 * useAdminImports.js - 后台页面公共引入
 * 
 * 统一管理后台页面中重复使用的导入内容，
 * 减少代码重复，便于维护和更新。
 */

// Vue 组合式 API
export { ref, computed, watch } from 'vue';

// Vue Router
export { useRouter, useRoute } from 'vue-router';

// Vue I18n
export { useI18n } from 'vue-i18n';

// Lucide Vue Next 图标（后台常用图标）
export {
  // 基础操作图标
  Plus,
  Search,
  Edit3,
  Trash2,
  Eye,
  EyeOff,
  Check,
  X,
  Save,
  RefreshCw,
  
  // 导航图标
  ChevronLeft,
  ChevronRight,
  ChevronUp,
  ChevronDown,
  
  // 功能图标
  Filter,
  Clock,
  Tag,
  Users,
  User,
  UserPlus,
  Mail,
  Shield,
  FileText,
  Folder,
  FolderKanban,
  BookOpen,
  Play,
  HardDrive,
  Settings,
  Sliders,
  Archive,
  Info,
  LayoutDashboard,
  MessageSquare,
  BarChart3,
  Download,
  Upload,
  Image,
  Video,
  Calendar,
  TrendingUp,
  Globe,
  Palette,
  Bell,
  Moon,
  Sun,
  Zap,
  Monitor,
  
  // 状态图标
  CheckCircle,
  XCircle,
  AlertCircle,
  AlertTriangle,

  // 媒体库新增
  LayoutGrid,
  List,

  // 菜单管理新增
  GripVertical,
  ExternalLink,
  RotateCcw,

  // 评论管理新增
  Send,
  CornerDownRight,
  ShieldCheck,
  Reply,

  // 广告管理新增
  Link,
  MousePointer,
  Maximize,
  Sidebar,
  SkipForward,

  // 关于页面新增
  Code,
  Cpu,
  Terminal,
  Coffee,
  Github,
  Heart,
  Star,
  Layers,
  History,
  FileCode,
  ArrowRight,
  Layout
} from 'lucide-vue-next';

// 主题 composable
export { useTheme } from './useTheme';

// 通知 composable
export { useToast } from './useToast';

// 日期工具函数
export { formatToShort, formatToEnglish } from '../utils/dateUtils';

// 类型转换工具函数
export { findById, formatId } from '../utils/typeConvert';

// 分类工具函数
export { getCategoryLabel, getCategoryNames } from '../utils/categoryUtils';

// 数据文件
export { adminUsers } from '../data/users';
export { categories as adminCategories } from '../data/categories';
export { adminTags } from '../data/tags';
export { adminLogs } from '../data/logs';
// 菜单数据已迁移到 useMenuItems composable
// 角色和权限相关函数已迁移到 useRolePermissions composable

// 组件
export { default as ConfirmDialog } from '../components/admin/ConfirmDialog.vue';
export { default as ContentForm } from '../components/admin/ContentForm.vue';
export { default as RoleForm } from '../components/admin/RoleForm.vue';
export { default as UserForm } from '../components/admin/UserForm.vue';
export { default as MetaForm } from '../components/admin/MetaForm.vue';
export { default as TagInput } from '../components/admin/TagInput.vue';
export { default as AdminPagination } from '../components/admin/AdminPagination.vue';
export { default as MediaPreviewModal } from '../components/admin/MediaPreviewModal.vue';
export { default as MediaUploadModal } from '../components/admin/MediaUploadModal.vue';