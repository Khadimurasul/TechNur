import React from "react";
import { siteConfig } from "@/data/siteConfig";
import { Server, Shield, Zap, Code2 } from "lucide-react";

const highlights = [
  {
    icon: Server,
    title: "Full-Stack Development",
    description: "Building complete web apps from frontend to backend with React & Supabase.",
  },
  {
    icon: Shield,
    title: "Type Safety",
    description: "Writing robust, maintainable code with TypeScript and modern patterns.",
  },
  {
    icon: Zap,
    title: "Performance",
    description: "Creating fast, optimized applications with Vite and best practices.",
  },
  {
    icon: Code2,
    title: "Beautiful UI",
    description: "Crafting pixel-perfect interfaces with Tailwind CSS and shadcn/ui.",
  },
];

const AboutSection: React.FC = () => {
  return (
    <section id="about" className="section-container">
      <div className="max-w-6xl mx-auto">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-sm font-mono text-primary mb-2">// ABOUT ME</h2>
          <h3 className="text-3xl sm:text-4xl font-bold mb-4">
            Crafting <span className="text-gradient">Modern Web</span> Experiences
          </h3>
        </div>

        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Bio Column */}
          <div className="space-y-6">
            <div className="p-6 bg-card border border-border rounded-xl">
              <div className="flex items-center gap-2 mb-4">
                <div className="flex gap-1.5">
                  <span className="w-3 h-3 rounded-full bg-destructive" />
                  <span className="w-3 h-3 rounded-full bg-yellow-500" />
                  <span className="w-3 h-3 rounded-full bg-green-500" />
                </div>
                <span className="text-xs font-mono text-muted-foreground">about.md</span>
              </div>
              <div className="font-mono text-sm leading-relaxed text-muted-foreground whitespace-pre-line">
                {siteConfig.bioLong}
              </div>
            </div>

            {/* Contact Info */}
            <div className="flex flex-wrap gap-4">
              <a
                href={`tel:${siteConfig.phone}`}
                className="flex items-center gap-2 px-4 py-2 bg-secondary border border-border rounded-lg hover:border-primary/50 transition-colors"
              >
                <span className="text-primary">📞</span>
                <span className="text-sm font-mono">{siteConfig.phoneFormatted}</span>
              </a>
              <a
                href={`mailto:${siteConfig.email}`}
                className="flex items-center gap-2 px-4 py-2 bg-secondary border border-border rounded-lg hover:border-primary/50 transition-colors"
              >
                <span className="text-primary">✉️</span>
                <span className="text-sm font-mono">{siteConfig.email}</span>
              </a>
            </div>
          </div>

          {/* Highlights Grid */}
          <div className="grid sm:grid-cols-2 gap-4">
            {highlights.map((item, index) => (
              <div
                key={item.title}
                className="card-glow p-6 bg-card border border-border rounded-xl transition-all duration-300 hover:border-primary/30"
                style={{ animationDelay: `${index * 100}ms` }}
              >
                <div className="w-12 h-12 mb-4 rounded-lg bg-gradient-primary flex items-center justify-center">
                  <item.icon className="w-6 h-6 text-primary-foreground" />
                </div>
                <h4 className="text-lg font-semibold mb-2">{item.title}</h4>
                <p className="text-sm text-muted-foreground">{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection;
