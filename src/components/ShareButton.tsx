import React, { useState } from "react";
import { Button } from "@/components/ui/button";
import { Share2, X, MessageCircle, Linkedin, Copy, Check } from "lucide-react";
import { toast } from "@/hooks/use-toast";
import { siteConfig } from "@/data/siteConfig";

interface ShareButtonProps {
  title?: string;
  description?: string;
  url?: string;
  variant?: "floating" | "inline";
}

const ShareButton: React.FC<ShareButtonProps> = ({
  title = `${siteConfig.name} - ${siteConfig.role}`,
  description = siteConfig.tagline,
  url = typeof window !== "undefined" ? window.location.href : siteConfig.siteUrl,
  variant = "floating",
}) => {
  const [isOpen, setIsOpen] = useState(false);
  const [copied, setCopied] = useState(false);

  const shareData = {
    title,
    text: description,
    url,
  };

  const handleNativeShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share(shareData);
      } catch (err) {
        if ((err as Error).name !== "AbortError") {
          console.error("Share failed:", err);
        }
      }
    } else {
      setIsOpen(true);
    }
  };

  const shareLinks = {
    whatsapp: `https://wa.me/?text=${encodeURIComponent(`${title} - ${description} ${url}`)}`,
    twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(`${title} - ${description}`)}&url=${encodeURIComponent(url)}`,
    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
  };

  const copyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(url);
      setCopied(true);
      toast({
        title: "Link copied!",
        description: "The link has been copied to your clipboard.",
      });
      setTimeout(() => setCopied(false), 2000);
    } catch (err) {
      toast({
        title: "Failed to copy",
        description: "Please copy the link manually.",
        variant: "destructive",
      });
    }
  };

  const openShareWindow = (shareUrl: string) => {
    window.open(shareUrl, "_blank", "width=600,height=400,noopener,noreferrer");
  };

  if (variant === "inline") {
    return (
      <div className="flex items-center gap-2">
        <Button
          variant="ghost"
          size="icon"
          onClick={() => openShareWindow(shareLinks.whatsapp)}
          className="text-muted-foreground hover:text-primary"
          aria-label="Share on WhatsApp"
        >
          <MessageCircle className="w-4 h-4" />
        </Button>
        <Button
          variant="ghost"
          size="icon"
          onClick={() => openShareWindow(shareLinks.twitter)}
          className="text-muted-foreground hover:text-primary"
          aria-label="Share on X"
        >
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
        </Button>
        <Button
          variant="ghost"
          size="icon"
          onClick={() => openShareWindow(shareLinks.linkedin)}
          className="text-muted-foreground hover:text-primary"
          aria-label="Share on LinkedIn"
        >
          <Linkedin className="w-4 h-4" />
        </Button>
        <Button
          variant="ghost"
          size="icon"
          onClick={copyToClipboard}
          className="text-muted-foreground hover:text-primary"
          aria-label="Copy link"
        >
          {copied ? <Check className="w-4 h-4" /> : <Copy className="w-4 h-4" />}
        </Button>
      </div>
    );
  }

  return (
    <div className="floating-action">
      {isOpen && (
        <div className="absolute bottom-16 right-0 p-4 bg-card border border-border rounded-xl shadow-lg animate-scale-in min-w-[200px]">
          <div className="flex justify-between items-center mb-3">
            <span className="text-sm font-medium">Share</span>
            <Button
              variant="ghost"
              size="icon"
              className="h-6 w-6"
              onClick={() => setIsOpen(false)}
            >
              <X className="w-4 h-4" />
            </Button>
          </div>
          <div className="grid grid-cols-4 gap-2">
            <Button
              variant="ghost"
              size="icon"
              onClick={() => openShareWindow(shareLinks.whatsapp)}
              className="hover:bg-green-500/10 hover:text-green-500"
              aria-label="Share on WhatsApp"
            >
              <MessageCircle className="w-5 h-5" />
            </Button>
            <Button
              variant="ghost"
              size="icon"
              onClick={() => openShareWindow(shareLinks.twitter)}
              className="hover:bg-primary/10 hover:text-primary"
              aria-label="Share on X"
            >
              <svg className="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </Button>
            <Button
              variant="ghost"
              size="icon"
              onClick={() => openShareWindow(shareLinks.linkedin)}
              className="hover:bg-blue-500/10 hover:text-blue-500"
              aria-label="Share on LinkedIn"
            >
              <Linkedin className="w-5 h-5" />
            </Button>
            <Button
              variant="ghost"
              size="icon"
              onClick={copyToClipboard}
              className="hover:bg-primary/10 hover:text-primary"
              aria-label="Copy link"
            >
              {copied ? <Check className="w-5 h-5" /> : <Copy className="w-5 h-5" />}
            </Button>
          </div>
        </div>
      )}
      <Button
        onClick={handleNativeShare}
        size="icon"
        className="h-14 w-14 rounded-full bg-gradient-primary text-primary-foreground shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
        aria-label="Share this page"
      >
        <Share2 className="w-6 h-6" />
      </Button>
    </div>
  );
};

export default ShareButton;
