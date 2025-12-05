import React from "react";
import { projects, siteConfig } from "@/data/siteConfig";
import { Button } from "@/components/ui/button";
import { ExternalLink, Github, Star } from "lucide-react";
import ShareButton from "@/components/ShareButton";

const ProjectsSection: React.FC = () => {
  return (
    <section id="projects" className="section-container">
      <div className="max-w-6xl mx-auto">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-sm font-mono text-primary mb-2">// PROJECTS</h2>
          <h3 className="text-3xl sm:text-4xl font-bold mb-4">
            Featured <span className="text-gradient">Work</span>
          </h3>
          <p className="text-muted-foreground max-w-2xl mx-auto">
            A selection of projects showcasing my backend development expertise.
          </p>
        </div>

        {/* Projects Grid */}
        <div className="grid md:grid-cols-2 gap-6">
          {projects.map((project, index) => (
            <article
              key={project.id}
              className="group card-glow bg-card border border-border rounded-xl overflow-hidden transition-all duration-300 hover:border-primary/30"
            >
              {/* Project Header */}
              <div className="p-6 border-b border-border">
                <div className="flex items-start justify-between mb-4">
                  <div className="flex items-center gap-2">
                    {project.featured && (
                      <span className="flex items-center gap-1 px-2 py-1 text-xs font-medium bg-primary/10 text-primary rounded-full">
                        <Star className="w-3 h-3" />
                        Featured
                      </span>
                    )}
                  </div>
                  <ShareButton
                    variant="inline"
                    title={`${project.title} by ${siteConfig.name}`}
                    description={project.description}
                    url={`${siteConfig.siteUrl}/projects/${project.id}`}
                  />
                </div>
                <h4 className="text-xl font-bold mb-2 group-hover:text-primary transition-colors">
                  {project.title}
                </h4>
                <p className="text-muted-foreground text-sm leading-relaxed">
                  {project.description}
                </p>
              </div>

              {/* Tech Stack */}
              <div className="p-6 pt-4">
                <div className="flex flex-wrap gap-2 mb-4">
                  {project.tech.map((tech) => (
                    <span
                      key={tech}
                      className="px-3 py-1 text-xs font-mono bg-secondary text-muted-foreground rounded-full"
                    >
                      {tech}
                    </span>
                  ))}
                </div>

                {/* Links */}
                <div className="flex gap-2">
                  <Button variant="outline" size="sm" asChild className="flex-1">
                    <a
                      href={project.repoUrl}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      <Github className="w-4 h-4" />
                      Code
                    </a>
                  </Button>
                  <Button variant="hero" size="sm" asChild className="flex-1">
                    <a
                      href={project.liveUrl}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      <ExternalLink className="w-4 h-4" />
                      Live Demo
                    </a>
                  </Button>
                </div>
              </div>
            </article>
          ))}
        </div>

        {/* View More CTA */}
        <div className="text-center mt-12">
          <Button variant="outline" size="lg" asChild>
            <a
              href={siteConfig.social.github}
              target="_blank"
              rel="noopener noreferrer"
            >
              <Github className="w-5 h-5" />
              View All Projects on GitHub
            </a>
          </Button>
        </div>
      </div>
    </section>
  );
};

export default ProjectsSection;
