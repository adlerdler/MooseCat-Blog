/**
 * useRolePermissions.js - 角色权限管理
 *
 * 基于 Spatie/laravel-permission 方案 + 扩展字段。
 *
 * 功能说明：
 * - 统一管理角色和权限的相关操作函数
 * - 提供角色信息获取、权限检查等功能
 * - 支持 Spatie 标准字段 (name, guard_name)
 * - 支持扩展字段 (color, label, program_id)
 * 
 * ⚠️ 角色管理说明（2026-05-24）：
 * - users 表的 role_id 字段已删除，改用 Spatie RBAC 的 model_has_roles 中间表管理角色
 * - 本 composable 仅用于前端展示参考，实际角色管理通过 Spatie 方法
 * - 分配角色：$user->assignRole('admin')
 * - 检查角色：$user->hasRole('admin')
 * - 获取角色：$user->getRoleNames()
 */
import { roles } from '../data/roles';
import { permissions } from '../data/permissions';
import { rolePermissions } from '../data/role_permissions';

export const useRolePermissions = () => {
  const COLOR_MAP = {
    red: { bg: 'bg-red-600', text: 'text-white', border: 'border-red-500' },
    blue: { bg: 'bg-blue-600', text: 'text-white', border: 'border-blue-500' },
    green: { bg: 'bg-green-600', text: 'text-white', border: 'border-green-500' },
    purple: { bg: 'bg-purple-600', text: 'text-white', border: 'border-purple-500' },
    gray: { bg: 'bg-gray-600', text: 'text-white', border: 'border-gray-500' },
    yellow: { bg: 'bg-yellow-500', text: 'text-gray-900', border: 'border-yellow-400' },
    cyan: { bg: 'bg-cyan-600', text: 'text-white', border: 'border-cyan-500' },
    orange: { bg: 'bg-orange-600', text: 'text-white', border: 'border-orange-500' },
    pink: { bg: 'bg-pink-600', text: 'text-white', border: 'border-pink-500' }
  };

  const GUARD_LABELS = {
    web: 'Web',
    api: 'API',
    admin: 'Admin'
  };

  const getRoleById = (roleId) => {
    return roles.find(r => r.id === roleId);
  };

  const getRoleByName = (name) => {
    return roles.find(r => r.name === name);
  };

  const getRole = (roleIdentifier) => {
    if (typeof roleIdentifier === 'number') {
      return roles.find(r => r.id === roleIdentifier);
    } else {
      return roles.find(r => r.name === roleIdentifier);
    }
  };

  const getRoleLabel = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.label : 'Unknown';
  };

  const getRoleColor = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.color : 'gray';
  };

  const getRoleGuardName = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.guard_name : 'web';
  };

  const getRoleDescription = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.description : '未知角色';
  };

  const getRoleStyle = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    const color = role ? role.color : 'gray';
    const colorConfig = COLOR_MAP[color] || COLOR_MAP.gray;
    return `${colorConfig.bg} ${colorConfig.text} ${colorConfig.border}`;
  };

  const getRoleColorConfig = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    const color = role ? role.color : 'gray';
    return COLOR_MAP[color] || COLOR_MAP.gray;
  };

  const availablePermissions = permissions.map(p => ({
    id: p.id,
    name: p.name,
    label: p.label,
    guard_name: p.guard_name,
    program_id: p.program_id
  }));

  const getPermissionById = (permissionId) => {
    return permissions.find(p => p.id === permissionId);
  };

  const getPermissionByName = (name) => {
    return permissions.find(p => p.name === name);
  };

  const getPermissionIdsByRoleId = (roleId) => {
    return rolePermissions
      .filter(rp => rp.role_id === roleId)
      .map(rp => rp.permission_id);
  };

  const getRoleIdsByPermissionId = (permissionId) => {
    return rolePermissions
      .filter(rp => rp.permission_id === permissionId)
      .map(rp => rp.role_id);
  };

  const hasPermission = (roleId, permissionId) => {
    return rolePermissions.some(rp => rp.role_id === roleId && rp.permission_id === permissionId);
  };

  const hasPermissionByName = (roleId, permissionName) => {
    const permission = getPermissionByName(permissionName);
    if (!permission) return false;
    return hasPermission(roleId, permission.id);
  };

  const getPermissionsByGuard = (guardName) => {
    return permissions.filter(p => p.guard_name === guardName);
  };

  const getRolesByGuard = (guardName) => {
    return roles.filter(r => r.guard_name === guardName);
  };

  const adminRoles = roles;

  return {
    roles,
    adminRoles,
    permissions,
    availablePermissions,
    rolePermissions,
    COLOR_MAP,
    GUARD_LABELS,

    getRoleById,
    getRoleByName,
    getRole,
    getRoleLabel,
    getRoleColor,
    getRoleGuardName,
    getRoleDescription,
    getRoleStyle,
    getRoleColorConfig,

    getPermissionById,
    getPermissionByName,
    getPermissionsByGuard,
    getRolesByGuard,

    getPermissionIdsByRoleId,
    getRoleIdsByPermissionId,
    hasPermission,
    hasPermissionByName
  };
};
