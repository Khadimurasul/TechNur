import React, { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { ArrowDown, Download, Mail, FolderGit2 } from "lucide-react";
import { siteConfig } from "@/data/siteConfig";

const HeroSection: React.FC = () => {
  const [displayText, setDisplayText] = useState("");
  const fullText = siteConfig.bio;

  useEffect(() => {
    let index = 0;
    const timer = setInterval(() => {
      if (index <= fullText.length) {
        setDisplayText(fullText.slice(0, index));
        index++;
      } else {
        clearInterval(timer);
      }
    }, 30);
    return () => clearInterval(timer);
  }, []);

  const scrollToSection = (id: string) => {
    document.querySelector(id)?.scrollIntoView({ behavior: "smooth" });
  };

  return (
    <section
      id="hero"
      className="relative min-h-screen flex items-center justify-center overflow-hidden"
      style={{ background: "var(--gradient-hero)" }}
    >
      {/* Background Grid Pattern */}
      <div className="absolute inset-0 opacity-[0.03]">
        <div
          className="absolute inset-0"
          style={{
            backgroundImage: `linear-gradient(hsl(var(--primary)) 1px, transparent 1px),
                             linear-gradient(90deg, hsl(var(--primary)) 1px, transparent 1px)`,
            backgroundSize: "50px 50px",
          }}
        />
      </div>

      {/* Gradient Orbs */}
      <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl" />
      <div className="absolute bottom-1/4 right-1/4 w-96 h-96 bg-accent/10 rounded-full blur-3xl" />

      <div className="container mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div className="max-w-4xl mx-auto text-center">
          {/* Terminal-style intro */}
          <div className="inline-flex items-center gap-2 px-4 py-2 mb-6 bg-secondary/50 border border-border rounded-full text-sm font-mono text-muted-foreground animate-fade-up">
            <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
            <span>Available for work</span>
          </div>

          {/* Name */}
          <h1 className="text-5xl sm:text-6xl lg:text-7xl font-bold mb-4 animate-fade-up animation-delay-100">
            Hi, I'm{" "}
            <span className="text-gradient">{siteConfig.name}</span>
          </h1>

          {/* Role */}
          <h2 className="text-2xl sm:text-3xl lg:text-4xl font-medium text-muted-foreground mb-6 animate-fade-up animation-delay-200">
            {siteConfig.role}
          </h2>

          {/* Typing effect bio */}
          <div className="max-w-2xl mx-auto mb-10 animate-fade-up animation-delay-300">
            <p className="text-lg text-muted-foreground font-mono leading-relaxed">
              <span className="text-primary">{">"}</span>
              {displayText}
              <span className="terminal-cursor" />
            </p>
          </div>

          {/* CTA Buttons */}
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-up animation-delay-400">
            <Button
              size="lg"
              variant="gradient"
              onClick={() => scrollToSection("#projects")}
              className="w-full sm:w-auto"
            >
              <FolderGit2 className="w-5 h-5" />
              View Projects
            </Button>
            <Button
              size="lg"
              variant="hero"
              onClick={() => scrollToSection("#contact")}
              className="w-full sm:w-auto"
            >
              <Mail className="w-5 h-5" />
              Contact Me
            </Button>
            <Button
              size="lg"
              variant="outline"
              className="w-full sm:w-auto"
              onClick={async () => {
                try {
                  const { cvApi } = await import("@/lib/api");
                  const downloadUrl = cvApi.getDownloadUrl();
                  window.open(downloadUrl, '_blank');
                } catch (error) {
                  // Fallback to static CV if API fails
                  window.open(siteConfig.cvUrl, '_blank');
                }
              }}
            >
              <Download className="w-5 h-5" />
              Download CV
            </Button>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-3 gap-8 mt-16 pt-16 border-t border-border/50 animate-fade-up animation-delay-500">
            <div className="text-center">
              <div className="text-3xl sm:text-4xl font-bold text-gradient mb-1">
                {siteConfig.yearsOfExperience}+
              </div>
              <div className="text-sm text-muted-foreground">Years Experience</div>
            </div>
            <div className="text-center">
              <div className="text-3xl sm:text-4xl font-bold text-gradient mb-1">
                {siteConfig.projectsCompleted}+
              </div>
              <div className="text-sm text-muted-foreground">Projects Completed</div>
            </div>
            <div className="text-center">
              <div className="text-3xl sm:text-4xl font-bold text-gradient mb-1">
                {siteConfig.coffeeConsumed}+
              </div>
              <div className="text-sm text-muted-foreground">Cups of Coffee</div>
            </div>
          </div>
        </div>
      </div>

      {/* Scroll indicator */}
      <div className="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce-subtle">
        <button
          onClick={() => scrollToSection("#about")}
          className="p-2 rounded-full border border-border hover:border-primary transition-colors"
          aria-label="Scroll down"
        >
          <ArrowDown className="w-5 h-5 text-muted-foreground" />
        </button>
      </div>
    </section>
  );
};

export default HeroSection;
