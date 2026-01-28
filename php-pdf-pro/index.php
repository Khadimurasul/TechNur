<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NitroPDF Pro (PHP) - TechNur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .ribbon-tab { @apply px-4 py-1 cursor-pointer hover:bg-blue-600 transition-colors; }
        .ribbon-tab.active { @apply bg-white text-blue-700 font-semibold; }
        .ribbon-button { @apply flex flex-col items-center justify-center p-2 rounded hover:bg-blue-50 text-gray-700 min-w-[64px] transition-all; }
        .ribbon-button i { @apply text-2xl mb-1 text-blue-600; }
        .ribbon-button span { @apply text-[11px]; }
        .tool-panel { display: none; }
        .tool-panel.active { display: block; }
    </style>
</head>
<body class="bg-gray-200 h-screen flex flex-col overflow-hidden">

    <!-- Top Status Bar -->
    <div class="bg-blue-800 text-white text-[11px] px-3 py-0.5 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-file-pdf"></i>
            <span>NitroPDF Pro (PHP Edition) - TechNur</span>
        </div>
        <div class="flex items-center space-x-4">
            <span id="status-msg">Ready</span>
        </div>
    </div>

    <!-- Ribbon Tabs -->
    <div class="bg-blue-700 text-white flex items-end px-2 space-x-1">
        <div class="ribbon-tab active" data-tab="home">Home</div>
        <div class="ribbon-tab" data-tab="convert">Convert</div>
        <div class="ribbon-tab" data-tab="review">Review</div>
        <div class="ribbon-tab" data-tab="layout">Page Layout</div>
        <div class="ribbon-tab" data-tab="forms">Forms</div>
        <div class="ribbon-tab" data-tab="share">Share</div>
        <div class="ribbon-tab" data-tab="erase">Erase</div>
        <div class="ribbon-tab" data-tab="protect">Protect</div>
    </div>

    <!-- Ribbon Content -->
    <div class="bg-white border-b border-gray-300 shadow-sm p-1.5 flex items-center space-x-1 overflow-x-auto h-[85px]">

        <!-- Sidebar Group (Common) -->
        <div class="flex space-x-0.5 border-r border-gray-200 pr-2 mr-2">
            <button class="ribbon-button">
                <i class="fa-solid fa-hand"></i>
                <span>Hand</span>
            </button>
            <button class="ribbon-button active bg-blue-50">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit</span>
            </button>
            <div class="flex flex-col items-center">
                <button class="ribbon-button">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                    <span>Zoom <i class="fa-solid fa-caret-down text-[8px]"></i></span>
                </button>
            </div>
        </div>

        <!-- Home Tab Content -->
        <div id="tab-home" class="tab-content flex space-x-1">
            <div class="flex flex-col items-center border-r border-gray-200 pr-2 mr-2">
                <label class="ribbon-button cursor-pointer">
                    <input type="file" id="file-upload" class="hidden" multiple accept=".pdf">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Open</span>
                </label>
                <div class="text-[9px] text-gray-400 mt-0.5 uppercase">File</div>
            </div>
            <div class="flex space-x-1 border-r border-gray-200 pr-2 mr-2">
                <button class="ribbon-button" onclick="showAction('merge')">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Combine</span>
                </button>
                <button class="ribbon-button" onclick="showAction('extract')">
                    <i class="fa-solid fa-file-export"></i>
                    <span>Extract</span>
                </button>
                <div class="text-[9px] text-gray-400 mt-auto uppercase text-center w-full">Create</div>
            </div>
            <button class="ribbon-button" onclick="showAction('annotate')">
                <i class="fa-solid fa-font"></i>
                <span>Add Text</span>
            </button>
        </div>

        <!-- Erase Tab Content -->
        <div id="tab-erase" class="tab-content hidden flex space-x-1">
            <div class="flex flex-col items-center border-r border-gray-200 pr-2 mr-2">
                <button class="ribbon-button" onclick="showAction('whiteout')">
                    <i class="fa-solid fa-eraser"></i>
                    <span>Whiteout</span>
                </button>
                <div class="text-[9px] text-gray-400 mt-0.5 uppercase">Mask</div>
            </div>
            <div class="flex flex-col items-center border-r border-gray-200 pr-2 mr-2">
                <div class="flex space-x-1">
                    <button class="ribbon-button" onclick="showAction('redact')">
                        <i class="fa-solid fa-marker"></i>
                        <span>Redact</span>
                    </button>
                    <button class="ribbon-button opacity-50 cursor-not-allowed">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search</span>
                    </button>
                </div>
                <div class="text-[9px] text-gray-400 mt-0.5 uppercase">Redact</div>
            </div>
            <div class="flex flex-col items-center">
                <button class="ribbon-button" onclick="showAction('remove_metadata')">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Security</span>
                </button>
                <div class="text-[9px] text-gray-400 mt-0.5 uppercase text-center">Security</div>
            </div>
        </div>

        <!-- Organize Tab (Moved to Layout/Home) -->
        <div id="tab-layout" class="tab-content hidden flex space-x-1">
            <button class="ribbon-button" onclick="showAction('rotate')">
                <i class="fa-solid fa-rotate"></i>
                <span>Rotate</span>
            </button>
            <button class="ribbon-button" onclick="showAction('delete')">
                <i class="fa-solid fa-trash-can"></i>
                <span>Delete</span>
            </button>
            <div class="text-[9px] text-gray-400 mt-auto uppercase">Pages</div>
        </div>

    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Sidebar -->
        <div class="w-64 bg-gray-50 border-r border-gray-300 flex flex-col">
            <div class="p-2 border-b bg-gray-100 font-semibold text-xs text-gray-600">FILES</div>
            <div id="file-list" class="flex-1 overflow-y-auto p-2 space-y-1 text-xs">
                <div class="text-gray-400 text-center italic mt-4">No files opened</div>
            </div>
        </div>

        <!-- Viewport -->
        <div class="flex-1 bg-gray-300 relative flex flex-col items-center justify-center p-4">
            <div id="pdf-viewer-placeholder" class="bg-white shadow-2xl w-full h-full max-w-4xl flex flex-col items-center justify-center text-gray-300">
                <i class="fa-solid fa-file-pdf text-9xl mb-4"></i>
                <p class="text-xl font-light">Drag and drop files here to begin</p>
            </div>
            <iframe id="pdf-viewer" class="hidden w-full h-full max-w-4xl bg-white shadow-2xl" src=""></iframe>
        </div>

        <!-- Action Panel (Right) -->
        <div id="action-panel" class="w-80 bg-white border-l border-gray-300 hidden flex flex-col shadow-lg">
            <div class="p-3 border-b bg-blue-50 flex justify-between items-center">
                <h3 id="action-title" class="font-bold text-blue-800 uppercase text-sm">Action</h3>
                <button onclick="closeAction()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-4 flex-1 overflow-y-auto" id="action-form-container">
                <!-- Forms injected here -->
            </div>
            <div class="p-4 border-t bg-gray-50">
                <button id="process-btn" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-bold shadow-md">PROCESS</button>
            </div>
        </div>

    </div>

    <script>
        let currentFiles = [];
        let activeAction = '';

        // Tab Switching
        document.querySelectorAll('.ribbon-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.ribbon-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const tabName = tab.dataset.tab;
                document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
                const target = document.getElementById('tab-' + tabName);
                if (target) target.classList.remove('hidden');
            });
        });

        // File Upload
        document.getElementById('file-upload').addEventListener('change', async (e) => {
            const files = e.target.files;
            if (files.length === 0) return;

            const formData = new FormData();
            for (let file of files) {
                formData.append('pdfs[]', file);
            }

            setStatus('Uploading...');
            const res = await fetch('upload.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                currentFiles = [...currentFiles, ...data.files];
                updateFileList();
                setStatus('Files uploaded');
                if (currentFiles.length > 0) {
                    showPreview(currentFiles[0].path);
                }
            } else {
                alert('Upload failed: ' + data.error);
                setStatus('Upload failed');
            }
        });

        function updateFileList() {
            const list = document.getElementById('file-list');
            list.innerHTML = '';
            currentFiles.forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-2 bg-white border rounded hover:border-blue-300 cursor-pointer transition-all';
                item.innerHTML = `
                    <span class="truncate pr-2" title="${file.name}">${file.name}</span>
                    <button onclick="removeFile(${index})" class="text-gray-300 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                `;
                item.onclick = (e) => {
                    if (e.target.tagName !== 'BUTTON' && e.target.parentElement.tagName !== 'BUTTON') {
                        showPreview(file.path);
                    }
                };
                list.appendChild(item);
            });
        }

        function removeFile(index) {
            currentFiles.splice(index, 1);
            updateFileList();
            if (currentFiles.length === 0) {
                hidePreview();
            }
        }

        function showPreview(path) {
            const viewer = document.getElementById('pdf-viewer');
            const placeholder = document.getElementById('pdf-viewer-placeholder');
            viewer.src = path;
            viewer.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }

        function hidePreview() {
            const viewer = document.getElementById('pdf-viewer');
            const placeholder = document.getElementById('pdf-viewer-placeholder');
            viewer.src = '';
            viewer.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }

        function showAction(action) {
            activeAction = action;
            const panel = document.getElementById('action-panel');
            const title = document.getElementById('action-title');
            const container = document.getElementById('action-form-container');

            panel.classList.remove('hidden');
            title.innerText = action.toUpperCase();

            let html = '';
            switch(action) {
                case 'merge':
                    html = `<p class="text-xs text-gray-500 mb-4">Combines all files in the sidebar in order.</p>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-700">Output Filename</label>
                                <input type="text" id="output-name" class="w-full border p-2 rounded text-sm" value="merged_document.pdf">
                            </div>`;
                    break;
                case 'extract':
                case 'delete':
                    html = `<p class="text-xs text-gray-500 mb-4">${action === 'extract' ? 'Extract specific pages' : 'Remove specific pages'} from the selected file.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Select File</label>
                                    <select id="select-file" class="w-full border p-2 rounded text-sm">${currentFiles.map(f => `<option value="${f.path}">${f.name}</option>`).join('')}</select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Page Range (e.g. 1,2,5-8)</label>
                                    <input type="text" id="page-range" class="w-full border p-2 rounded text-sm" placeholder="1-3">
                                </div>
                            </div>`;
                    break;
                case 'rotate':
                    html = `<p class="text-xs text-gray-500 mb-4">Rotate all pages in the selected file.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Select File</label>
                                    <select id="select-file" class="w-full border p-2 rounded text-sm">${currentFiles.map(f => `<option value="${f.path}">${f.name}</option>`).join('')}</select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Degrees</label>
                                    <select id="rotate-degrees" class="w-full border p-2 rounded text-sm">
                                        <option value="90">90° Clockwise</option>
                                        <option value="180">180°</option>
                                        <option value="270">270° Counter-Clockwise</option>
                                    </select>
                                </div>
                            </div>`;
                    break;
                case 'annotate':
                    html = `<p class="text-xs text-gray-500 mb-4">Add a text overlay to all pages.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Select File</label>
                                    <select id="select-file" class="w-full border p-2 rounded text-sm">${currentFiles.map(f => `<option value="${f.path}">${f.name}</option>`).join('')}</select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Text to Add</label>
                                    <input type="text" id="anno-text" class="w-full border p-2 rounded text-sm" placeholder="DRAFT">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700">X (mm)</label>
                                        <input type="number" id="anno-x" class="w-full border p-2 rounded text-sm" value="10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700">Y (mm)</label>
                                        <input type="number" id="anno-y" class="w-full border p-2 rounded text-sm" value="10">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Page Number (Optional)</label>
                                    <input type="number" id="anno-page" class="w-full border p-2 rounded text-sm" placeholder="All pages">
                                </div>
                            </div>`;
                    break;
                case 'whiteout':
                case 'redact':
                    html = `<p class="text-xs text-gray-500 mb-4">${action === 'whiteout' ? 'Mask' : 'Redact'} content with a ${action === 'whiteout' ? 'white' : 'black'} box.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Select File</label>
                                    <select id="select-file" class="w-full border p-2 rounded text-sm">${currentFiles.map(f => `<option value="${f.path}">${f.name}</option>`).join('')}</select>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700">X (mm)</label>
                                        <input type="number" id="rect-x" class="w-full border p-2 rounded text-sm" value="10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700">Y (mm)</label>
                                        <input type="number" id="rect-y" class="w-full border p-2 rounded text-sm" value="10">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700">Width (mm)</label>
                                        <input type="number" id="rect-w" class="w-full border p-2 rounded text-sm" value="50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700">Height (mm)</label>
                                        <input type="number" id="rect-h" class="w-full border p-2 rounded text-sm" value="10">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Page Number (Optional)</label>
                                    <input type="number" id="rect-page" class="w-full border p-2 rounded text-sm" placeholder="All pages">
                                </div>
                            </div>`;
                    break;
                case 'remove_metadata':
                    html = `<p class="text-xs text-gray-500 mb-4">Strip all metadata from the document.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Select File</label>
                                    <select id="select-file" class="w-full border p-2 rounded text-sm">${currentFiles.map(f => `<option value="${f.path}">${f.name}</option>`).join('')}</select>
                                </div>
                            </div>`;
                    break;
            }
            container.innerHTML = html;
        }

        function closeAction() {
            document.getElementById('action-panel').classList.add('hidden');
            activeAction = '';
        }

        function setStatus(msg) {
            document.getElementById('status-msg').innerText = msg;
        }

        document.getElementById('process-btn').addEventListener('click', async () => {
            if (!activeAction) return;
            if (currentFiles.length === 0) {
                alert('Please upload files first');
                return;
            }

            const payload = {
                action: activeAction,
                files: currentFiles.map(f => f.path)
            };

            if (activeAction === 'merge') {
                payload.outputName = document.getElementById('output-name').value;
            } else if (activeAction === 'extract' || activeAction === 'delete') {
                payload.file = document.getElementById('select-file').value;
                payload.range = document.getElementById('page-range').value;
            } else if (activeAction === 'rotate') {
                payload.file = document.getElementById('select-file').value;
                payload.rotation = document.getElementById('rotate-degrees').value;
            } else if (activeAction === 'annotate') {
                payload.file = document.getElementById('select-file').value;
                payload.text = document.getElementById('anno-text').value;
                payload.x = document.getElementById('anno-x').value;
                payload.y = document.getElementById('anno-y').value;
                payload.page = document.getElementById('anno-page').value;
            } else if (activeAction === 'whiteout' || activeAction === 'redact') {
                payload.file = document.getElementById('select-file').value;
                payload.x = document.getElementById('rect-x').value;
                payload.y = document.getElementById('rect-y').value;
                payload.w = document.getElementById('rect-w').value;
                payload.h = document.getElementById('rect-h').value;
                payload.page = document.getElementById('rect-page').value;
            } else if (activeAction === 'remove_metadata') {
                payload.file = document.getElementById('select-file').value;
            }

            setStatus('Processing...');
            const res = await fetch('process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                setStatus('Success!');
                window.open(data.downloadUrl, '_blank');
            } else {
                alert('Process failed: ' + data.error);
                setStatus('Error');
            }
        });
    </script>
</body>
</html>
