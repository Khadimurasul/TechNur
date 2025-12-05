export const siteConfig = {
  name: "TechNur",
  role: "Full-Stack Developer",
  tagline: "Building modern, responsive web applications with React & TypeScript",
  phone: "+233553389962",
  phoneFormatted: "+233 55 338 9962",
  email: "technur18@gmail.com",
  bio: "I build modern web applications using React, TypeScript, and Tailwind CSS. Experienced with full-stack development, Supabase, and creating beautiful user experiences.",
  bioLong: `With a passion for modern web development, I specialize in creating responsive, performant applications using the latest technologies. My expertise spans from crafting pixel-perfect UIs with React and Tailwind CSS to building robust backends with Supabase and Node.js.

I'm dedicated to writing clean, maintainable TypeScript code and creating seamless user experiences. Whether it's a landing page, SaaS application, or complex dashboard, I deliver solutions that are both beautiful and functional.`,
  yearsOfExperience: 5,
  projectsCompleted: 50,
  coffeeConsumed: 2500,
  siteUrl: "https://technur.dev",
  ogImage: "/og-image.png",
  social: {
    github: "https://github.com/Khadimurasul",
    linkedin: "https://linkedin.com/in/Ali Hadim",
    twitter: "https://twitter.com/Khadimurasul_",
  },
  cvUrl: "/assets/TechNur-CV.pdf",
};

export const skills = [
  { name: "React", icon: "react", category: "Framework" },
  { name: "TypeScript", icon: "typescript", category: "Language" },
  { name: "Tailwind CSS", icon: "tailwind", category: "Styling" },
  { name: "Vite", icon: "vite", category: "Build Tool" },
  { name: "Supabase", icon: "supabase", category: "Backend" },
  { name: "PostgreSQL", icon: "postgresql", category: "Database" },
  { name: "Node.js", icon: "nodejs", category: "Runtime" },
  { name: "Next.js", icon: "nextjs", category: "Framework" },
  { name: "shadcn/ui", icon: "shadcn", category: "Components" },
  { name: "Framer Motion", icon: "framer", category: "Animation" },
  { name: "Git", icon: "git", category: "Version Control" },
  { name: "Figma", icon: "figma", category: "Design" },
];

export const projects = [
  {
    id: "saas-dashboard",
    title: "SaaS Analytics Dashboard",
    description: "Full-featured analytics dashboard with real-time data visualization, user authentication, and subscription management.",
    tech: ["React", "TypeScript", "Supabase", "Tailwind"],
    repoUrl: "https://github.com/technur/saas-dashboard",
    liveUrl: "https://dashboard.technur.dev",
    featured: true,
  },
  {
    id: "ecommerce-store",
    title: "Modern E-Commerce Store",
    description: "Complete e-commerce solution with cart functionality, Stripe payments, and inventory management.",
    tech: ["Next.js", "TypeScript", "Supabase", "Stripe"],
    repoUrl: "https://github.com/technur/ecommerce-store",
    liveUrl: "https://shop.technur.dev",
    featured: true,
  },
  {
    id: "realtime-chat",
    title: "Real-time Chat Application",
    description: "Modern chat app with real-time messaging, typing indicators, and media sharing using Supabase Realtime.",
    tech: ["React", "Supabase", "Tailwind", "Framer Motion"],
    repoUrl: "https://github.com/technur/chat-app",
    liveUrl: "https://chat.technur.dev",
    featured: true,
  },
  {
    id: "task-manager",
    title: "Task Management App",
    description: "Kanban-style task manager with drag-and-drop, team collaboration, and progress tracking.",
    tech: ["React", "TypeScript", "shadcn/ui", "Supabase"],
    repoUrl: "https://github.com/technur/task-manager",
    liveUrl: "https://tasks.technur.dev",
    featured: false,
  },
];

export const blogPosts = [
  {
    id: "react-best-practices",
    title: "React Best Practices in 2024",
    excerpt: "Modern patterns and techniques for building scalable React applications with TypeScript.",
    date: "2024-11-15",
    readTime: "8 min",
  },
  {
    id: "supabase-auth-guide",
    title: "Complete Supabase Auth Guide",
    excerpt: "Setting up authentication with Supabase including social logins, magic links, and row-level security.",
    date: "2024-10-28",
    readTime: "6 min",
  },
  {
    id: "tailwind-tips",
    title: "Advanced Tailwind CSS Tips",
    excerpt: "Level up your Tailwind skills with custom plugins, animations, and design system patterns.",
    date: "2024-10-10",
    readTime: "10 min",
  },
];
