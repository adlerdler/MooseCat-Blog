export const adminRoles = [
  { id: 1, name: 'Administrator', description: 'Full system access', userCount: 1, permissions: ['All Permissions'] },
  { id: 2, name: 'Editor', description: 'Content management access', userCount: 3, permissions: ['Posts', 'Videos', 'Projects', 'Resources'] },
  { id: 3, name: 'Author', description: 'Create and edit own content', userCount: 5, permissions: ['Create Posts', 'Edit Own Posts'] },
  { id: 4, name: 'Moderator', description: 'Comment moderation access', userCount: 2, permissions: ['Comments', 'Users'] },
  { id: 5, name: 'Subscriber', description: 'Read-only access', userCount: 12, permissions: ['View Content'] },
  { id: 6, name: 'API User', description: 'API access only', userCount: 1, permissions: ['API Access'] },
];