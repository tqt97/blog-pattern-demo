export function uploadWithProgress(url, fileOrBlob, originalName = null, onProgress = null, onDone = null, onError = null) {
    const form = new FormData();
    form.append('file', fileOrBlob, originalName || 'image.jpg');

    const xhr = new XMLHttpRequest();
    xhr.open("POST", url);

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
    }

    xhr.setRequestHeader('Accept', 'application/json');

    xhr.upload.addEventListener("progress", e => {
        if (e.lengthComputable && typeof onProgress === 'function') {
            onProgress(Math.round((e.loaded / e.total) * 100));
        }
    });

    xhr.onload = () => {
        if (xhr.status === 422) {
            try {
                const res = JSON.parse(xhr.responseText);

                let msg = 'Validation failed';

                if (res.errors && typeof res.errors === 'object') {
                    if (res.errors.file && res.errors.file.length) {
                        msg = res.errors.file[0];
                    } else {
                        const keys = Object.keys(res.errors);
                        if (keys.length > 0 && res.errors[keys[0]].length > 0) {
                            msg = res.errors[keys[0]][0];
                        }
                    }
                } else if (res.message) {
                    msg = res.message;
                }

                onError && onError(msg);
            } catch (e) {
                onError && onError('Validation failed');
            }
            return;
        }

        // Success (2xx)
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const res = JSON.parse(xhr.responseText);
                onDone && onDone(res);
            } catch (e) {
                console.error('Invalid JSON:', xhr.responseText);
                onError && onError('Invalid JSON response');
            }
            return;
        }

        // errors (500, 413, ...)
        console.error('Upload error:', xhr.status, xhr.responseText);
        onError && onError('Upload failed with status ' + xhr.status);
    };

    xhr.onerror = () => {
        onError && onError('Network error');
    };

    xhr.send(form);
}

export function resizeImage(file, maxWidth = 1280) {
    return new Promise(resolve => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement("canvas");
                const scale = maxWidth / img.width;
                const newW = img.width > maxWidth ? maxWidth : img.width;
                const newH = img.width > maxWidth ? img.height * scale : img.height;

                canvas.width = newW;
                canvas.height = newH;

                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, newW, newH);

                canvas.toBlob(blob => resolve(blob), "image/jpeg", 0.8);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

export function validateImage(file, maxSizeMb = 4) {
    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        return 'Only JPG / PNG / WEBP allowed.';
    }
    if (file.size > maxSizeMb * 1024 * 1024) {
        return `Image must be < ${maxSizeMb}MB`;
    }
    return null;
}

export function singleUploader({ uploadUrl, fieldName, deleteUrl = null }) {
    return {
        fieldName,
        uploadUrl,
        deleteUrl,

        preview: null,
        path: null,
        pathRelative: null,
        dragging: false,
        loading: false,
        error: null,
        progress: 0,

        async processFile(file) {
            this.error = validateImage(file, 4);
            if (this.error) return;

            this.loading = true;
            this.progress = 0;

            const reader = new FileReader();
            reader.onload = e => this.preview = e.target.result;
            reader.readAsDataURL(file);

            const resized = await resizeImage(file, 1280);

            uploadWithProgress(
                this.uploadUrl,
                resized,
                file.name,
                (percent) => {
                    this.progress = percent;
                },
                (res) => {
                    this.path = res.relative ?? res.path;
                    this.pathRelative = res.relative ?? null;
                    this.loading = false;
                    this.progress = 100;
                },
                (err) => {
                    this.error = err;
                    this.loading = false;
                    this.progress = 0;
                }
            );
        },

        handleChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.processFile(file);
        },

        handleDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer.files[0];
            if (!file) return;
            this.processFile(file);
        },

        async remove() {
            if ((this.path || this.pathRelative) && this.deleteUrl) {
                try {
                    const token = document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content');

                    const response = await fetch(this.deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ path: this.pathRelative || this.path }),
                    });

                    if (!response.ok) {
                        console.warn('Failed to delete file on server', await response.text());
                    }
                } catch (e) {
                    console.warn('Failed to delete file on server', e);
                }
            }

            this.preview = null;
            this.path = null;
            this.pathRelative = null;
            this.error = null;
            this.progress = 0;
            this.loading = false;
        },
    }
}



// --- MULTI UPLOADER (gallery) ---

export function multipleUploader({ uploadUrl, initial = [], fieldName, deleteUrl = null, maxFiles = 10 }) {
    return {
        fieldName,
        uploadUrl,
        deleteUrl,
        maxFiles,

        items: [],        // { id, preview, path, pathRelative, progress, loading, error }
        dragging: false,
        error: null,

        init() {
            this.items = (initial || []).map((item, index) => {
                const url = typeof item === 'string' ? item : (item.path || '');
                return {
                    id: Date.now() + index,
                    preview: url,
                    path: url,
                    pathRelative: typeof item === 'object' ? (item.relative || null) : null,
                    progress: 100,
                    loading: false,
                    error: null,
                };
            });
        },

        handleChange(e) {
            const files = Array.from(e.target.files || []);
            if (!files.length) return;
            this.addFiles(files);
            e.target.value = ''; // reset input
        },

        handleDrop(e) {
            this.dragging = false;
            const files = Array.from(e.dataTransfer.files || []);
            if (!files.length) return;
            this.addFiles(files);
        },

        addFiles(files) {
            for (const file of files) {
                if (this.items.length >= this.maxFiles) {
                    this.error = `Maximum ${this.maxFiles} files allowed.`;
                    break;
                }

                const err = validateImage(file, 4);
                if (err) {
                    this.error = err;
                    continue;
                }

                const id = Date.now() + Math.random();

                const item = {
                    id,
                    preview: null,
                    path: null,
                    pathRelative: null,
                    progress: 0,
                    loading: true,
                    error: null,
                };

                this.items.push(item);
                this.uploadSingleFile(file, id);
            }
        },

        async uploadSingleFile(file, id) {
            // set preview
            const reader = new FileReader();
            reader.onload = e => {
                const idx = this.items.findIndex(i => i.id === id);
                if (idx !== -1) {
                    this.items[idx].preview = e.target.result;
                }
            };
            reader.readAsDataURL(file);

            const resized = await resizeImage(file, 1280);

            uploadWithProgress(
                this.uploadUrl,
                resized,
                file.name,
                (percent) => {
                    const idx = this.items.findIndex(i => i.id === id);
                    if (idx !== -1) {
                        this.items[idx].progress = percent;
                    }
                },
                (res) => {
                    const idx = this.items.findIndex(i => i.id === id);
                    if (idx !== -1) {
                        this.items[idx].path = res.path;
                        this.items[idx].pathRelative = res.relative ?? null;
                        this.items[idx].loading = false;
                        this.items[idx].progress = 100;
                    }
                },
                (err) => {
                    const idx = this.items.findIndex(i => i.id === id);
                    if (idx !== -1) {
                        this.items[idx].error = err;
                        this.items[idx].loading = false;
                    }
                }
            );
        },

        async remove(id) {
            const idx = this.items.findIndex(i => i.id === id);
            if (idx === -1) return;

            const item = this.items[idx];

            if ((item.path || item.pathRelative) && this.deleteUrl) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const response = await fetch(this.deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ path: item.pathRelative || item.path }),
                    });

                    if (!response.ok) {
                        console.warn('Failed to delete file on server', await response.text());
                    }
                } catch (e) {
                    console.warn('Failed to delete file on server', e);
                }
            }

            this.items.splice(idx, 1);
        },

        moveUp(id) {
            const idx = this.items.findIndex(i => i.id === id);
            if (idx > 0) {
                const tmp = this.items[idx - 1];
                this.items[idx - 1] = this.items[idx];
                this.items[idx] = tmp;
            }
        },

        moveDown(id) {
            const idx = this.items.findIndex(i => i.id === id);
            if (idx !== -1 && idx < this.items.length - 1) {
                const tmp = this.items[idx + 1];
                this.items[idx + 1] = this.items[idx];
                this.items[idx] = tmp;
            }
        },

        getValues() {
            return this.items.map(i => i.pathRelative || i.path);
        },
    }
}
