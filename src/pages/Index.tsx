import React from "react";
import { Helmet } from "react-helmet-async";
import Navbar from "@/components/Navbar";
import HeroSection from "@/components/sections/HeroSection";
import AboutSection from "@/components/sections/AboutSection";
import SkillsSection from "@/components/sections/SkillsSection";
import ProjectsSection from "@/components/sections/ProjectsSection";
import BlogSection from "@/components/sections/BlogSection";
import ContactSection from "@/components/sections/ContactSection";
import Footer from "@/components/Footer";
import ShareButton from "@/components/ShareButton";
import { siteConfig } from "@/data/siteConfig";

const Index: React.FC = () => {
  const structuredData = {
    "@context": "https://schema.org",
    "@type": "Person",
    name: siteConfig.name,
    jobTitle: siteConfig.role,
    description: siteConfig.tagline,
    email: siteConfig.email,
    telephone: siteConfig.phone,
    url: siteConfig.siteUrl,
    sameAs: [
      siteConfig.social.github,
      siteConfig.social.linkedin,
      siteConfig.social.twitter,
    ],
  };

  return (
    <>
      <Helmet>
        <title>{`${siteConfig.name} | ${siteConfig.role}`}</title>
        <meta name="description" content={siteConfig.tagline} />
        <meta name="author" content={siteConfig.name} />
        <link rel="canonical" href={siteConfig.siteUrl} />

        {/* Open Graph */}
        <meta property="og:type" content="website" />
        <meta property="og:url" content={siteConfig.siteUrl} />
        <meta property="og:title" content={`${siteConfig.name} | ${siteConfig.role}`} />
        <meta property="og:description" content={siteConfig.tagline} />
        <meta property="og:image" content={`${siteConfig.siteUrl}${siteConfig.ogImage}`} />

        {/* Twitter */}
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:url" content={siteConfig.siteUrl} />
        <meta name="twitter:title" content={`${siteConfig.name} | ${siteConfig.role}`} />
        <meta name="twitter:description" content={siteConfig.tagline} />
        <meta name="twitter:image" content={`${siteConfig.siteUrl}${siteConfig.ogImage}`} />

        {/* Structured Data */}
        <script type="application/ld+json">
          {JSON.stringify(structuredData)}
        </script>
      </Helmet>

      <div className="min-h-screen bg-background text-foreground">
        <Navbar />
        <main>
          <HeroSection />
          <AboutSection />
          <SkillsSection />
          <ProjectsSection />
          <BlogSection />
          <ContactSection />
        </main>
        <Footer />
        <ShareButton />
      </div>
    </>
  );
};

export default Index;
