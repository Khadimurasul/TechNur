import React from "react";
import { skills } from "@/data/siteConfig";
import {
  ReactIcon,
  TypescriptIcon,
  TailwindIcon,
  ViteIcon,
  SupabaseIcon,
  PostgresqlIcon,
  NodejsIcon,
  NextjsIcon,
  ShadcnIcon,
  FramerIcon,
  GitIcon,
  FigmaIcon,
} from "@/components/icons/TechIcons";

const iconMap: Record<string, React.FC<{ className?: string }>> = {
  react: ReactIcon,
  typescript: TypescriptIcon,
  tailwind: TailwindIcon,
  vite: ViteIcon,
  supabase: SupabaseIcon,
  postgresql: PostgresqlIcon,
  nodejs: NodejsIcon,
  nextjs: NextjsIcon,
  shadcn: ShadcnIcon,
  framer: FramerIcon,
  git: GitIcon,
  figma: FigmaIcon,
};

const SkillsSection: React.FC = () => {
  return (
    <section id="skills" className="section-container bg-card/30">
      <div className="max-w-6xl mx-auto">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-sm font-mono text-primary mb-2">// SKILLS</h2>
          <h3 className="text-3xl sm:text-4xl font-bold mb-4">
            My <span className="text-gradient">Tech Stack</span>
          </h3>
          <p className="text-muted-foreground max-w-2xl mx-auto">
            Modern tools and technologies for building beautiful, performant web applications.
          </p>
        </div>

        {/* Skills Grid */}
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          {skills.map((skill, index) => {
            const IconComponent = iconMap[skill.icon];
            return (
              <div
                key={skill.name}
                className="group flex flex-col items-center p-4 bg-card border border-border rounded-xl hover:border-primary/50 transition-all duration-300 hover:scale-105"
                style={{ animationDelay: `${index * 50}ms` }}
              >
                <div className="skill-icon mb-3 text-muted-foreground group-hover:text-primary">
                  {IconComponent && <IconComponent className="w-full h-full" />}
                </div>
                <span className="text-sm font-medium text-center">{skill.name}</span>
                <span className="text-xs text-muted-foreground">{skill.category}</span>
              </div>
            );
          })}
        </div>

        {/* Terminal-style code block */}
        <div className="mt-16 p-6 bg-background border border-border rounded-xl">
          <div className="flex items-center gap-2 mb-4">
            <div className="flex gap-1.5">
              <span className="w-3 h-3 rounded-full bg-destructive" />
              <span className="w-3 h-3 rounded-full bg-yellow-500" />
              <span className="w-3 h-3 rounded-full bg-green-500" />
            </div>
            <span className="text-xs font-mono text-muted-foreground">terminal</span>
          </div>
          <pre className="font-mono text-sm overflow-x-auto">
            <code>
              <span className="text-muted-foreground">$ </span>
              <span className="text-primary">npx</span>
              <span className="text-foreground"> create-react-app </span>
              <span className="text-accent">my-awesome-project</span>
              {"\n\n"}
              <span className="text-green-500">✓</span>
              <span className="text-muted-foreground"> Tech stack ready:</span>
              {"\n"}
              <span className="text-muted-foreground">  - React 18 with TypeScript</span>
              {"\n"}
              <span className="text-muted-foreground">  - Tailwind CSS + shadcn/ui</span>
              {"\n"}
              <span className="text-muted-foreground">  - Supabase for backend</span>
              {"\n"}
              <span className="text-muted-foreground">  - Vite for blazing fast builds</span>
            </code>
          </pre>
        </div>
      </div>
    </section>
  );
};

export default SkillsSection;
