import React from "react";
import { blogPosts } from "@/data/siteConfig";
import { ArrowRight, Calendar, Clock } from "lucide-react";

const BlogSection: React.FC = () => {
  return (
    <section id="blog" className="section-container bg-card/30">
      <div className="max-w-6xl mx-auto">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-sm font-mono text-primary mb-2">// BLOG</h2>
          <h3 className="text-3xl sm:text-4xl font-bold mb-4">
            Technical <span className="text-gradient">Notes</span>
          </h3>
          <p className="text-muted-foreground max-w-2xl mx-auto">
            Sharing insights and learnings from backend development.
          </p>
        </div>

        {/* Blog Posts Grid */}
        <div className="grid md:grid-cols-3 gap-6">
          {blogPosts.map((post, index) => (
            <article
              key={post.id}
              className="group bg-card border border-border rounded-xl p-6 hover:border-primary/30 transition-all duration-300"
            >
              {/* Meta */}
              <div className="flex items-center gap-4 text-xs text-muted-foreground mb-4">
                <span className="flex items-center gap-1">
                  <Calendar className="w-3 h-3" />
                  {new Date(post.date).toLocaleDateString("en-US", {
                    month: "short",
                    day: "numeric",
                    year: "numeric",
                  })}
                </span>
                <span className="flex items-center gap-1">
                  <Clock className="w-3 h-3" />
                  {post.readTime}
                </span>
              </div>

              {/* Title */}
              <h4 className="text-lg font-bold mb-3 group-hover:text-primary transition-colors">
                {post.title}
              </h4>

              {/* Excerpt */}
              <p className="text-sm text-muted-foreground mb-4 line-clamp-3">
                {post.excerpt}
              </p>

              {/* Read More */}
              <a
                href={`#blog/${post.id}`}
                className="inline-flex items-center gap-1 text-sm font-medium text-primary hover:gap-2 transition-all"
              >
                Read More
                <ArrowRight className="w-4 h-4" />
              </a>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
};

export default BlogSection;
