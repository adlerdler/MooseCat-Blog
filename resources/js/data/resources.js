/**
 * resources.js - 资源下载数据
 * 
 * 功能说明：
 * - 存储可供下载的设计资源信息
 * - 支持多种云盘来源
 * 
 * 数据字段：
 * - id: 资源唯一标识
 * - category: 分类（DESIGN/TYPOGRAPHY/CODE等）
 * - title: 资源标题
 * - description: 资源描述
 * - image: 预览图 URL
 * - format: 资源格式
 * - fileSize: 文件大小
 * - directLink: 直链
 * - drives: 云盘链接数组（google/baidu/ali）
 */
export const RESOURCES = [
  {
    id: 'r01',
    category: 'DESIGN',
    title: 'Constructivist UI Kit //',
    description: 'A complete UI kit based on constructivist design principles. Includes Figma files and React components for geometric layouts and brutalist typography.',
    image: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80',
    format: 'FIGMA + SOURCE',
    fileSize: '45 MB',
    directLink: '#',
    drives: [
      { type: 'google', link: 'https://drive.google.com/example-link' },
      { type: 'baidu', link: 'https://pan.baidu.com/s/example', password: 'adlr' },
      { type: 'ali', link: 'https://www.aliyundrive.com/s/example' },
    ]
  },
  {
    id: 'r02',
    category: 'TYPOGRAPHY',
    title: 'Geometric Type System //',
    description: 'A custom monospaced font family designed for structural interfaces and high-density data visualization.',
    image: 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=800&q=80',
    format: 'OTF + TTF + WOFF2',
    fileSize: '2.4 MB',
    drives: [
      { type: 'google', link: 'https://drive.google.com/example-type' },
      { type: 'baidu', link: 'https://pan.baidu.com/s/example2', password: 'font' },
    ]
  },
  {
    id: 'r03',
    category: 'CODE',
    title: 'Data Vis Components //',
    description: 'Raw D3.js bindings for React. Stripped of all styling, focusing purely on SVG path generation and algorithmic accuracy.',
    image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80',
    format: 'TYPESCRIPT',
    fileSize: '1.8 MB',
    directLink: '#',
    drives: [
      { type: 'ali', link: 'https://www.aliyundrive.com/s/example3' },
    ]
  }
];
