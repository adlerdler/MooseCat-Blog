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
  Check,
  X,
  Save,
  RefreshCw,
  
  // 导航图标
  ChevronLeft,
  ChevronRight,
  
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
  RotateCcw
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
export { adminUsers, getUserById, getAuthorName } from '../data/users';
export { adminRoles, getRoleLabel, getRoleStyle, getRoleByValue } from '../data/roles';
export { permissions, availablePermissions } from '../data/permissions';
export { getPermissionIdsByRoleId } from '../data/role_permissions';
export { adminCategories } from '../data/categories';
export { adminTags } from '../data/tags';
export { adminLogs } from '../data/logs';
export { adminMenuItems } from '../data/menu';

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