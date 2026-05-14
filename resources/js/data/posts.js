/**
 * posts.js - 博客文章数据
 * 
 * 功能说明：
 * - 存储所有博客文章的静态数据
 * - 包含文章内容、分类、标签等信息
 * 
 * 数据字段：
 * - id: 文章唯一标识
 * - title: 文章标题
 * - category: 分类（THEORY/DESIGN/TECHNOLOGY/CULTURE）
 * - date: 发布日期
 * - author: 作者
 * - excerpt: 摘要
 * - content: Markdown 格式的文章内容
 * - color: 卡片颜色主题（red/black）
 * - tags: 标签数组
 */
export const POSTS = [
  {
    id: '1',
    title: 'THE GEOMETRY OF PERCEPTION',
    category: 'THEORY',
    date: '2026-04-28T10:30:00',
    author: 'ADLER DECHT',
    excerpt: 'Exploring how structural design influences cognitive processes in modern interfaces.',
    content: `# The Geometry of Perception

The human brain is wired to find patterns in chaos. In this long-form exploration, we dive into the structural foundations of user interface design through the lens of Constructivist theory.

## THE GRID AS A SKELETON

Every interface has a skeleton. Whether acknowledged or not, the underlying structure dictates the user journey.

\`\`\`javascript
// Grid system example
const grid = {
  columns: 12,
  gutter: 24,
  container: 1200,
  breakpoints: {
    sm: 640,
    md: 768,
    lg: 1024,
    xl: 1280
  }
};

function calculateColumn(width, breakpoint) {
  const available = grid.breakpoints[breakpoint] - (grid.gutter * 2);
  return Math.floor(width / (available / grid.columns));
}
\`\`\`

### Key Principles

The grid provides:
- **Visual anchor points** - Consistent reference for alignment
- **Predictable navigation patterns** - Users know where to look
- **Hierarchical information organization** - Clear visual weight distribution
- **Emotional comfort through familiarity** - Reduced cognitive load

> "Geometry is not just aesthetic; it is the grammar of visual communication."
> — Constructivist Design Principles

## MODULAR CONSTRUCTION

Modern interfaces are built from modular components. Each element should function independently while contributing to the greater whole.

\`\`\`vue
<template>
  <div class="card">
    <slot name="header" />
    <slot name="content" />
    <slot name="footer" />
  </div>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'default'
  }
});
<\/script>

<style scoped>
.card {
  border: 4px solid var(--construct-black);
  padding: 24px;
  background: white;
}
<\/style>
\`\`\`

This approach allows for:

1. **Rapid prototyping** - Assemble interfaces quickly
2. **Consistent branding** - Uniform design language
3. **Scalable architecture** - Add features without breaking things
4. **Maintainable codebases** - Easy to update and debug

## PERCEPTUAL HIERARCHY

Understanding how users perceive information is crucial. Size, color, and placement all influence how content is processed.

\`\`\`css
/* Hierarchy through typography */
.h1 {
  font-size: 4rem;
  font-weight: 900;
  letter-spacing: -0.05em;
  text-transform: uppercase;
}

.h2 {
  font-size: 2.5rem;
  font-weight: 800;
  letter-spacing: -0.025em;
  text-transform: uppercase;
}

.body {
  font-size: 1.125rem;
  line-height: 1.8;
  font-weight: 500;
}
\`\`\`

The constructivist approach emphasizes intentional design decisions that guide the user's attention naturally. By applying these principles, we create interfaces that communicate effectively and resonate on a deeper cognitive level.

---

*Published: April 28, 2026*
*Author: Adler Decht*`,
    color: 'red',
    tags: ['UI', 'Cognition', 'Geometry'],
  },
  {
    id: '2',
    title: 'TYPOGRAPHY AS ARCHITECTURE',
    category: 'DESIGN',
    date: '2026-04-20T14:00:00',
    author: 'RODCHENKO',
    excerpt: 'Why fonts are the literal beams and columns of a digital experience.',
    content: `# Typography as Architecture

Typography is the physical material of digital communication. If a layout is the blueprint, the typeface is the concrete and steel.

## THE WEIGHT OF THE WORD

Boldness is not just about emphasis; it is about hierarchy. When everything is loud, nothing is heard.

\`\`\`scss
// Font weight scale
$font-weights: (
  'thin': 100,
  'light': 300,
  'regular': 400,
  'medium': 500,
  'bold': 700,
  'black': 900
);

@each $name, $weight in $font-weights {
  .font-#{$name} {
    font-weight: $weight;
  }
}
\`\`\`

### Structural Integrity

Different font weights support the "roof" of the message:
- **Black (900)** - Primary headings, maximum impact
- **Bold (700)** - Secondary headings, strong emphasis
- **Medium (500)** - Body text, optimal readability
- **Regular (400)** - Captions, secondary information

> "A typeface should be like a building: solid, structured, and purposeful."
> — Alexander Rodchenko

## MONOSPACE AS TRUTH

Rodchenko famously used type as a weapon of clarity. Monospaced fonts are the most honest choices for a data-driven society.

\`\`\`javascript
// Typography system configuration
const typography = {
  display: {
    family: 'Space Grotesk',
    weights: [700],
    tracking: '-0.05em'
  },
  body: {
    family: 'Inter',
    weights: [400, 500, 600],
    lineHeight: 1.8
  },
  code: {
    family: 'JetBrains Mono',
    weights: [400, 700],
    lineHeight: 1.6
  }
};

function getFontStack(type) {
  const config = typography[type];
  return \`\${config.family}, system-ui, sans-serif\`;
}
\`\`\`

## TYPOGRAPHIC GRID

Combining grid systems with typographic rhythm creates visual harmony.

\`\`\`css
/* Baseline grid */
html {
  font-size: 16px;
  line-height: 1.5;
}

body {
  --baseline: 24px;
  line-height: var(--baseline);
}

h1, h2, h3, h4 {
  margin-bottom: var(--baseline);
}

p {
  margin-bottom: var(--baseline);
}
\`\`\`

The technical breakdown reveals why monospaced fonts and sans-serif grotesques form the foundation of constructivist digital design.

---

*Published: April 20, 2026*
*Author: Rodchenko*`,
    color: 'red',
    tags: ['Type', 'Rodchenko', 'Structure'],
  },
  {
    id: '3',
    title: 'MANIFESTO OF THE MACHINE',
    category: 'CULTURE',
    date: '2026-04-15T00:00:00',
    author: 'ADLERIAN',
    excerpt: 'Redefining the relationship between human intuition and algorithmic precision.',
    content: `# Manifesto of the Machine

We live in an age of the hybrid. The machine provides the precision, the human provide the purpose.

## ALGORITHMIC HONESTY

When we hide complexity, we strip away user agency. "Open-hood" design reveals internal logic.

\`\`\`python
# Algorithm transparency example
class ContentRecommender:
    def __init__(self, user_preferences):
        self.user_preferences = user_preferences
        self.weights = {
            'recency': 0.3,
            'relevance': 0.5,
            'popularity': 0.2
        }

    def recommend(self, items):
        """Transparent recommendation algorithm."""
        scored_items = []
        for item in items:
            score = self._calculate_score(item)
            scored_items.append({
                'item': item,
                'score': score,
                'breakdown': self._explain_score(item)
            })
        return sorted(scored_items, key=lambda x: x['score'], reverse=True)

    def _calculate_score(self, item):
        return sum(
            self.weights[factor] * self._factor_score(factor, item)
            for factor in self.weights
        )

    def _explain_score(self, item):
        return {
            factor: self._factor_score(factor, item)
            for factor in self.weights
        }
\`\`\`

## THE HYBRID FUTURE

This manifesto calls for a new digital culture that celebrates the mechanical rather than hiding it.

### Design Principles

- **Don't make it "natural"** - Make it readable
- **Show your work** - Algorithms should be transparent
- **Human in the loop** - Machines augment, don't replace
- **Structure > Aesthetics** - Function defines form

\`\`\`typescript
// Human-in-the-loop system
interface HumanDecision {
  timestamp: Date;
  userId: string;
  action: 'approve' | 'reject' | 'modify';
  feedback: string;
}

class HybridSystem {
  private decisions: HumanDecision[] = [];

  process(input: any, humanInLoop: boolean = true) {
    const machineResult = this.machineProcess(input);

    if (!humanInLoop) {
      return machineResult;
    }

    return this.presentForHumanReview(machineResult);
  }

  recordDecision(decision: HumanDecision) {
    this.decisions.push(decision);
    this.learnFromHuman(decision);
  }
}
\`\`\`

> "We should not aim to make technology 'natural'—we should aim to make it readable."
> — Adlerian Manifesto

We should not aim to make technology "natural"—we should aim to make it readable. The machine provides the precision, the human provides the purpose.

---

*Published: April 15, 2026*
*Author: Adlerian*`,
    color: 'black',
    tags: ['Manifesto', 'Machine', 'Algorithmic'],
  },
  {
    id: '4',
    title: 'ARCHITECTURAL VECTORS',
    category: 'SYSTEM-DESIGN',
    date: '2026-04-12T00:00:00',
    author: 'V. TATLIN',
    excerpt: 'Analyzing the trajectory of lines in three-dimensional digital space.',
    content: `# Architectural Vectors

Vectors are the invisible strings that hold our digital world together. In this study, we bridge the gap between physical gravity and digital directional force.

## VECTOR MATHEMATICS

Understanding the math behind movement is fundamental.

\`\`\`javascript
// 2D Vector class
class Vector2 {
  constructor(x = 0, y = 0) {
    this.x = x;
    this.y = y;
  }

  add(v) {
    return new Vector2(this.x + v.x, this.y + v.y);
  }

  subtract(v) {
    return new Vector2(this.x - v.x, this.y - v.y);
  }

  multiply(scalar) {
    return new Vector2(this.x * scalar, this.y * scalar);
  }

  magnitude() {
    return Math.sqrt(this.x * this.x + this.y * this.y);
  }

  normalize() {
    const mag = this.magnitude();
    return mag > 0 ? this.multiply(1 / mag) : new Vector2();
  }

  dot(v) {
    return this.x * v.x + this.y * v.y;
  }
}

// Usage
const position = new Vector2(100, 100);
const velocity = new Vector2(5, 3);
const newPosition = position.add(velocity);
\`\`\`

### Directional Force

Vectors define:
- **Position** - Where something is
- **Velocity** - Where it's going
- **Acceleration** - How fast it's getting there
- **Force** - What's pushing it

\`\`\`css
/* Vector-based animations */
@keyframes construct-move {
  0% {
    transform: translate(0, 0) rotate(0deg);
  }
  50% {
    transform: translate(100px, 50px) rotate(45deg);
  }
  100% {
    transform: translate(0, 0) rotate(0deg);
  }
}

.vector-element {
  animation: construct-move 3s ease-in-out infinite;
}
\`\`\`

## VISUAL COMPOSITION

Using vectors to create dynamic compositions.

\`\`\`javascript
// Dynamic layout with vectors
function createFlowLayout(elements, startPoint, direction) {
  let current = startPoint;
  const spacing = new Vector2(20, 20);

  return elements.map((element, index) => {
    const position = current.add(direction.multiply(index * 80));
    current = current.add(spacing);
    return { element, position };
  });
}
\`\`\`

> "A vector is not just a line—it's a story about movement."
> — Vladimir Tatlin

Vectors bridge the gap between physical gravity and digital directional force, creating compositions that feel alive yet structured.

---

*Published: April 12, 2026*
*Author: V. Tatlin*`,
    color: 'red',
    tags: ['Vectors', 'Tatlin', 'Space'],
  },
  {
    id: '5',
    title: 'THE ETHICS OF THE GRID',
    category: 'ENGINEERING',
    date: '2026-04-10T00:00:00',
    author: 'ADLER DECHT',
    excerpt: 'Is the grid a tool of liberation or a cage for creativity?',
    content: `# The Ethics of the Grid

The grid provides structure, but at what cost? We explore the philosophical implications of strict alignment in UX design.

## LIBERATION VS CONSTRAINT

\`\`\`javascript
// Grid system with flexibility
class AdaptiveGrid {
  constructor(options = {}) {
    this.columns = options.columns || 12;
    this.gutter = options.gutter || 24;
    this.strict = options.strict || false;
  }

  layout(children) {
    if (this.strict) {
      return this.strictLayout(children);
    }
    return this.flexibleLayout(children);
  }

  strictLayout(children) {
    return children.map((child, i) => ({
      ...child,
      column: (i % this.columns) + 1,
      span: 1
    }));
  }

  flexibleLayout(children) {
    return children.map((child) => ({
      ...child,
      column: child.preferredColumn || 1,
      span: child.preferredSpan || Math.min(4, this.columns)
    }));
  }
}
\`\`\`

### Philosophical Questions

- **Does structure empower or limit?**
- **Can creativity thrive within constraints?**
- **Who benefits from the grid?**
- **What gets excluded?**

\`\`\`css
/* Grid with intentional breaks */
.grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 24px;
}

.grid-item {
  grid-column: span 3;
}

/* Intentional break - creates visual tension */
.grid-item.featured {
  grid-column: span 8;
  margin-left: -24px;
  border: 4px solid #ef4444;
}
\`\`\`

## DESIGN JUSTICE

> "A grid should be a framework for expression, not a prison for creativity."
> — Design Justice Principles

The grid is neither inherently good nor bad—it's a tool whose ethics depend on how it's deployed.

---

*Published: April 10, 2026*
*Author: Adler Decht*`,
    color: 'black',
    tags: ['UX', 'Ethics', 'Grid'],
  },
];
