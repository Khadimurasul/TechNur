import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { toast } from '@/hooks/use-toast';
import { messagesApi, projectsApi, cvApi } from '@/lib/api';
import { LogOut, Mail, Trash2, Plus, Edit, Eye, EyeOff, Upload, FileText } from 'lucide-react';
import { format } from 'date-fns';

interface Message {
  id: number;
  name: string;
  email: string;
  message: string;
  created_at: string;
  read: number;
}

interface Project {
  id: number;
  title: string;
  description: string;
  tech: string;
  repoUrl?: string;
  liveUrl?: string;
  imageUrl?: string;
  featured: number;
  created_at: string;
}

const AdminDashboard: React.FC = () => {
  const navigate = useNavigate();
  const [messages, setMessages] = useState<Message[]>([]);
  const [projects, setProjects] = useState<Project[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedMessage, setSelectedMessage] = useState<Message | null>(null);
  const [isProjectDialogOpen, setIsProjectDialogOpen] = useState(false);
  const [editingProject, setEditingProject] = useState<Project | null>(null);
  const [cvInfo, setCvInfo] = useState<{ filename: string; original_filename: string; uploaded_at: string; file_size: number } | null>(null);
  const [isUploadingCv, setIsUploadingCv] = useState(false);
  const [projectForm, setProjectForm] = useState({
    title: '',
    description: '',
    tech: '',
    repoUrl: '',
    liveUrl: '',
    imageUrl: '',
    featured: false,
  });

  useEffect(() => {
    const token = localStorage.getItem('admin_token');
    if (!token) {
      navigate('/admin/login');
      return;
    }
    loadData();
  }, [navigate]);

  const loadData = async () => {
    try {
      setIsLoading(true);
      const [messagesRes, projectsRes, cvRes] = await Promise.all([
        messagesApi.getAll(),
        projectsApi.getAll(),
        cvApi.getAdminInfo().catch(() => ({ cv: null })),
      ]);
      setMessages(messagesRes.messages || []);
      setProjects(projectsRes.projects || []);
      setCvInfo(cvRes.cv || null);
    } catch (error) {
      toast({
        title: 'Error',
        description: 'Failed to load data',
        variant: 'destructive',
      });
    } finally {
      setIsLoading(false);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_username');
    navigate('/admin/login');
  };

  const handleDeleteMessage = async (id: number) => {
    if (!confirm('Are you sure you want to delete this message?')) return;

    try {
      await messagesApi.delete(id);
      setMessages(messages.filter((m) => m.id !== id));
      toast({
        title: 'Success',
        description: 'Message deleted',
      });
    } catch (error) {
      toast({
        title: 'Error',
        description: 'Failed to delete message',
        variant: 'destructive',
      });
    }
  };

  const handleMarkAsRead = async (id: number) => {
    try {
      await messagesApi.markAsRead(id);
      setMessages(
        messages.map((m) => (m.id === id ? { ...m, read: 1 } : m))
      );
    } catch (error) {
      toast({
        title: 'Error',
        description: 'Failed to update message',
        variant: 'destructive',
      });
    }
  };

  const handleProjectSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const projectData = {
        ...projectForm,
        tech: projectForm.tech.split(',').map((t) => t.trim()),
      };

      if (editingProject) {
        await projectsApi.update(editingProject.id, projectData);
        toast({
          title: 'Success',
          description: 'Project updated',
        });
      } else {
        await projectsApi.create(projectData);
        toast({
          title: 'Success',
          description: 'Project created',
        });
      }

      setIsProjectDialogOpen(false);
      setEditingProject(null);
      setProjectForm({
        title: '',
        description: '',
        tech: '',
        repoUrl: '',
        liveUrl: '',
        imageUrl: '',
        featured: false,
      });
      loadData();
    } catch (error) {
      toast({
        title: 'Error',
        description: 'Failed to save project',
        variant: 'destructive',
      });
    }
  };

  const handleEditProject = (project: Project) => {
    setEditingProject(project);
    setProjectForm({
      title: project.title,
      description: project.description,
      tech: project.tech,
      repoUrl: project.repoUrl || '',
      liveUrl: project.liveUrl || '',
      imageUrl: project.imageUrl || '',
      featured: project.featured === 1,
    });
    setIsProjectDialogOpen(true);
  };

  const handleDeleteProject = async (id: number) => {
    if (!confirm('Are you sure you want to delete this project?')) return;

    try {
      await projectsApi.delete(id);
      setProjects(projects.filter((p) => p.id !== id));
      toast({
        title: 'Success',
        description: 'Project deleted',
      });
    } catch (error) {
      toast({
        title: 'Error',
        description: 'Failed to delete project',
        variant: 'destructive',
      });
    }
  };

  const handleCvUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.type !== 'application/pdf') {
      toast({
        title: 'Error',
        description: 'Only PDF files are allowed',
        variant: 'destructive',
      });
      return;
    }

    if (file.size > 10 * 1024 * 1024) {
      toast({
        title: 'Error',
        description: 'File size must be less than 10MB',
        variant: 'destructive',
      });
      return;
    }

    setIsUploadingCv(true);
    try {
      const response = await cvApi.upload(file);
      setCvInfo({
        filename: response.filename,
        original_filename: response.originalFilename,
        uploaded_at: new Date().toISOString(),
        file_size: response.fileSize,
      });
      toast({
        title: 'Success',
        description: 'CV uploaded successfully',
      });
    } catch (error) {
      toast({
        title: 'Error',
        description: error instanceof Error ? error.message : 'Failed to upload CV',
        variant: 'destructive',
      });
    } finally {
      setIsUploadingCv(false);
      // Reset file input
      e.target.value = '';
    }
  };

  const unreadCount = messages.filter((m) => m.read === 0).length;

  return (
    <div className="min-h-screen bg-background">
      <div className="border-b">
        <div className="container mx-auto px-4 py-4 flex justify-between items-center">
          <h1 className="text-2xl font-bold">Admin Panel</h1>
          <Button variant="outline" onClick={handleLogout}>
            <LogOut className="w-4 h-4 mr-2" />
            Logout
          </Button>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        <Tabs defaultValue="messages" className="space-y-4">
          <TabsList>
            <TabsTrigger value="messages">
              Messages
              {unreadCount > 0 && (
                <Badge className="ml-2" variant="destructive">
                  {unreadCount}
                </Badge>
              )}
            </TabsTrigger>
            <TabsTrigger value="projects">Projects</TabsTrigger>
            <TabsTrigger value="cv">CV Management</TabsTrigger>
          </TabsList>

          <TabsContent value="messages" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Messages</CardTitle>
                <CardDescription>View and manage contact form messages</CardDescription>
              </CardHeader>
              <CardContent>
                {isLoading ? (
                  <div className="text-center py-8">Loading...</div>
                ) : messages.length === 0 ? (
                  <div className="text-center py-8 text-muted-foreground">
                    No messages yet
                  </div>
                ) : (
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Message</TableHead>
                        <TableHead>Date</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {messages.map((message) => (
                        <TableRow key={message.id}>
                          <TableCell className="font-medium">{message.name}</TableCell>
                          <TableCell>{message.email}</TableCell>
                          <TableCell>
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => setSelectedMessage(message)}
                            >
                              View
                            </Button>
                          </TableCell>
                          <TableCell>
                            {format(new Date(message.created_at), 'MMM d, yyyy')}
                          </TableCell>
                          <TableCell>
                            {message.read === 0 ? (
                              <Badge variant="secondary">Unread</Badge>
                            ) : (
                              <Badge variant="outline">Read</Badge>
                            )}
                          </TableCell>
                          <TableCell>
                            <div className="flex gap-2">
                              {message.read === 0 && (
                                <Button
                                  variant="ghost"
                                  size="sm"
                                  onClick={() => handleMarkAsRead(message.id)}
                                >
                                  <Eye className="w-4 h-4" />
                                </Button>
                              )}
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => handleDeleteMessage(message.id)}
                              >
                                <Trash2 className="w-4 h-4" />
                              </Button>
                            </div>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="projects" className="space-y-4">
            <Card>
              <CardHeader>
                <div className="flex justify-between items-center">
                  <div>
                    <CardTitle>Projects</CardTitle>
                    <CardDescription>Manage your portfolio projects</CardDescription>
                  </div>
                  <Dialog
                    open={isProjectDialogOpen}
                    onOpenChange={(open) => {
                      setIsProjectDialogOpen(open);
                      if (!open) {
                        setEditingProject(null);
                        setProjectForm({
                          title: '',
                          description: '',
                          tech: '',
                          repoUrl: '',
                          liveUrl: '',
                          imageUrl: '',
                          featured: false,
                        });
                      }
                    }}
                  >
                    <DialogTrigger asChild>
                      <Button>
                        <Plus className="w-4 h-4 mr-2" />
                        Add Project
                      </Button>
                    </DialogTrigger>
                    <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                      <DialogHeader>
                        <DialogTitle>
                          {editingProject ? 'Edit Project' : 'Add New Project'}
                        </DialogTitle>
                        <DialogDescription>
                          Fill in the details for your project
                        </DialogDescription>
                      </DialogHeader>
                      <form onSubmit={handleProjectSubmit} className="space-y-4">
                        <div className="space-y-2">
                          <Label htmlFor="title">Title *</Label>
                          <Input
                            id="title"
                            value={projectForm.title}
                            onChange={(e) =>
                              setProjectForm({ ...projectForm, title: e.target.value })
                            }
                            required
                          />
                        </div>
                        <div className="space-y-2">
                          <Label htmlFor="description">Description *</Label>
                          <Textarea
                            id="description"
                            value={projectForm.description}
                            onChange={(e) =>
                              setProjectForm({ ...projectForm, description: e.target.value })
                            }
                            rows={4}
                            required
                          />
                        </div>
                        <div className="space-y-2">
                          <Label htmlFor="tech">Technologies (comma-separated) *</Label>
                          <Input
                            id="tech"
                            value={projectForm.tech}
                            onChange={(e) =>
                              setProjectForm({ ...projectForm, tech: e.target.value })
                            }
                            placeholder="React, TypeScript, Tailwind"
                            required
                          />
                        </div>
                        <div className="space-y-2">
                          <Label htmlFor="repoUrl">Repository URL</Label>
                          <Input
                            id="repoUrl"
                            type="url"
                            value={projectForm.repoUrl}
                            onChange={(e) =>
                              setProjectForm({ ...projectForm, repoUrl: e.target.value })
                            }
                          />
                        </div>
                        <div className="space-y-2">
                          <Label htmlFor="liveUrl">Live URL</Label>
                          <Input
                            id="liveUrl"
                            type="url"
                            value={projectForm.liveUrl}
                            onChange={(e) =>
                              setProjectForm({ ...projectForm, liveUrl: e.target.value })
                            }
                          />
                        </div>
                        <div className="space-y-2">
                          <Label htmlFor="imageUrl">Image URL</Label>
                          <Input
                            id="imageUrl"
                            type="url"
                            value={projectForm.imageUrl}
                            onChange={(e) =>
                              setProjectForm({ ...projectForm, imageUrl: e.target.value })
                            }
                          />
                        </div>
                        <div className="flex items-center space-x-2">
                          <Switch
                            id="featured"
                            checked={projectForm.featured}
                            onCheckedChange={(checked) =>
                              setProjectForm({ ...projectForm, featured: checked })
                            }
                          />
                          <Label htmlFor="featured">Featured Project</Label>
                        </div>
                        <div className="flex justify-end gap-2">
                          <Button
                            type="button"
                            variant="outline"
                            onClick={() => setIsProjectDialogOpen(false)}
                          >
                            Cancel
                          </Button>
                          <Button type="submit">
                            {editingProject ? 'Update' : 'Create'} Project
                          </Button>
                        </div>
                      </form>
                    </DialogContent>
                  </Dialog>
                </div>
              </CardHeader>
              <CardContent>
                {isLoading ? (
                  <div className="text-center py-8">Loading...</div>
                ) : projects.length === 0 ? (
                  <div className="text-center py-8 text-muted-foreground">
                    No projects yet. Add your first project!
                  </div>
                ) : (
                  <div className="grid gap-4">
                    {projects.map((project) => (
                      <Card key={project.id}>
                        <CardHeader>
                          <div className="flex justify-between items-start">
                            <div>
                              <CardTitle className="flex items-center gap-2">
                                {project.title}
                                {project.featured === 1 && (
                                  <Badge>Featured</Badge>
                                )}
                              </CardTitle>
                              <CardDescription className="mt-2">
                                {project.description}
                              </CardDescription>
                            </div>
                            <div className="flex gap-2">
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => handleEditProject(project)}
                              >
                                <Edit className="w-4 h-4" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => handleDeleteProject(project.id)}
                              >
                                <Trash2 className="w-4 h-4" />
                              </Button>
                            </div>
                          </div>
                        </CardHeader>
                        <CardContent>
                          <div className="flex flex-wrap gap-2 mb-2">
                            {project.tech.split(',').map((tech, idx) => (
                              <Badge key={idx} variant="secondary">
                                {tech.trim()}
                              </Badge>
                            ))}
                          </div>
                          <div className="flex gap-2 text-sm text-muted-foreground">
                            {project.repoUrl && (
                              <a
                                href={project.repoUrl}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="hover:text-primary"
                              >
                                Repository
                              </a>
                            )}
                            {project.liveUrl && (
                              <a
                                href={project.liveUrl}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="hover:text-primary"
                              >
                                Live Demo
                              </a>
                            )}
                          </div>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="cv" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>CV Management</CardTitle>
                <CardDescription>Upload and manage your CV file</CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="space-y-4">
                  <div>
                    <Label htmlFor="cv-upload">Upload CV (PDF only, max 10MB)</Label>
                    <div className="mt-2">
                      <Input
                        id="cv-upload"
                        type="file"
                        accept="application/pdf"
                        onChange={handleCvUpload}
                        disabled={isUploadingCv}
                        className="cursor-pointer"
                      />
                    </div>
                    {isUploadingCv && (
                      <p className="text-sm text-muted-foreground mt-2">Uploading...</p>
                    )}
                  </div>

                  {cvInfo && (
                    <div className="p-4 bg-muted rounded-lg space-y-2">
                      <div className="flex items-center gap-2">
                        <FileText className="w-5 h-5" />
                        <span className="font-medium">Current CV</span>
                      </div>
                      <div className="text-sm space-y-1">
                        <p>
                          <span className="text-muted-foreground">Filename:</span>{' '}
                          {cvInfo.original_filename}
                        </p>
                        <p>
                          <span className="text-muted-foreground">Size:</span>{' '}
                          {(cvInfo.file_size / 1024 / 1024).toFixed(2)} MB
                        </p>
                        <p>
                          <span className="text-muted-foreground">Uploaded:</span>{' '}
                          {format(new Date(cvInfo.uploaded_at), 'PPpp')}
                        </p>
                      </div>
                      <div className="pt-2">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => {
                            window.open(cvApi.getDownloadUrl(), '_blank');
                          }}
                        >
                          <FileText className="w-4 h-4 mr-2" />
                          Preview CV
                        </Button>
                      </div>
                    </div>
                  )}

                  {!cvInfo && (
                    <div className="p-8 text-center border-2 border-dashed rounded-lg">
                      <FileText className="w-12 h-12 mx-auto mb-4 text-muted-foreground" />
                      <p className="text-muted-foreground">No CV uploaded yet</p>
                      <p className="text-sm text-muted-foreground mt-1">
                        Upload a PDF file to get started
                      </p>
                    </div>
                  )}
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>

      {/* Message Detail Dialog */}
      <Dialog open={!!selectedMessage} onOpenChange={() => setSelectedMessage(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Message Details</DialogTitle>
          </DialogHeader>
          {selectedMessage && (
            <div className="space-y-4">
              <div>
                <Label>From</Label>
                <p className="font-medium">{selectedMessage.name}</p>
                <p className="text-sm text-muted-foreground">{selectedMessage.email}</p>
              </div>
              <div>
                <Label>Date</Label>
                <p>{format(new Date(selectedMessage.created_at), 'PPpp')}</p>
              </div>
              <div>
                <Label>Message</Label>
                <p className="whitespace-pre-wrap">{selectedMessage.message}</p>
              </div>
              <div className="flex gap-2">
                <Button
                  variant="outline"
                  onClick={() => {
                    window.location.href = `mailto:${selectedMessage.email}`;
                  }}
                >
                  <Mail className="w-4 h-4 mr-2" />
                  Reply
                </Button>
                {selectedMessage.read === 0 && (
                  <Button onClick={() => handleMarkAsRead(selectedMessage.id)}>
                    <EyeOff className="w-4 h-4 mr-2" />
                    Mark as Read
                  </Button>
                )}
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default AdminDashboard;

