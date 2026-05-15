export const backupRecords = [
  {
    id: 1,
    name: 'full_backup_20260515_103000',
    type: 'full',
    status: 'completed',
    size: '256 MB',
    path: '/backups/full_backup_20260515_103000.zip',
    createdAt: '2026-05-15T10:30:00',
    completedAt: '2026-05-15T10:35:00',
    note: '完整备份 - 系统所有数据'
  },
  {
    id: 2,
    name: 'db_backup_20260514_220000',
    type: 'database',
    status: 'completed',
    size: '48 MB',
    path: '/backups/db_backup_20260514_220000.sql',
    createdAt: '2026-05-14T22:00:00',
    completedAt: '2026-05-14T22:05:00',
    note: '数据库备份'
  },
  {
    id: 3,
    name: 'files_backup_20260514_090000',
    type: 'files',
    status: 'completed',
    size: '180 MB',
    path: '/backups/files_backup_20260514_090000.tar.gz',
    createdAt: '2026-05-14T09:00:00',
    completedAt: '2026-05-14T09:15:00',
    note: '文件备份 - 上传媒体文件'
  },
  {
    id: 4,
    name: 'db_backup_20260513_220000',
    type: 'database',
    status: 'completed',
    size: '45 MB',
    path: '/backups/db_backup_20260513_220000.sql',
    createdAt: '2026-05-13T22:00:00',
    completedAt: '2026-05-13T22:04:00',
    note: '数据库备份'
  },
  {
    id: 5,
    name: 'incremental_backup_20260512',
    type: 'incremental',
    status: 'completed',
    size: '12 MB',
    path: '/backups/incremental_20260512.zip',
    createdAt: '2026-05-12T23:00:00',
    completedAt: '2026-05-12T23:02:00',
    note: '增量备份'
  },
  {
    id: 6,
    name: 'full_backup_20260510_100000',
    type: 'full',
    status: 'completed',
    size: '250 MB',
    path: '/backups/full_backup_20260510_100000.zip',
    createdAt: '2026-05-10T10:00:00',
    completedAt: '2026-05-10T10:08:00',
    note: '每周完整备份'
  }
];

export const backupTypes = {
  full: { zh: '完整备份', en: 'Full Backup', 'zh-TW': '完整備份' },
  database: { zh: '数据库备份', en: 'Database Backup', 'zh-TW': '數據庫備份' },
  files: { zh: '文件备份', en: 'Files Backup', 'zh-TW': '文件備份' },
  incremental: { zh: '增量备份', en: 'Incremental Backup', 'zh-TW': '增量備份' }
};

export const backupStatuses = {
  completed: { zh: '已完成', en: 'Completed', 'zh-TW': '已完成' },
  in_progress: { zh: '进行中', en: 'In Progress', 'zh-TW': '進行中' },
  failed: { zh: '失败', en: 'Failed', 'zh-TW': '失敗' }
};

export const getBackupTypeLabel = (type, locale = 'zh') => {
  const labels = backupTypes[type];
  if (!labels) return type;
  return labels[locale] || labels.zh;
};

export const getBackupStatusLabel = (status, locale = 'zh') => {
  const labels = backupStatuses[status];
  if (!labels) return status;
  return labels[locale] || labels.zh;
};
