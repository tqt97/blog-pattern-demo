import './bootstrap';

import Alpine from 'alpinejs';

import { singleUploader, multipleUploader } from './lib/uploader';

window.Alpine = Alpine;
window.singleUploader = singleUploader;
window.multipleUploader = multipleUploader;

Alpine.start();
