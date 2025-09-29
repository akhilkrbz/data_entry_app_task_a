import './bootstrap';
import Uppy from '@uppy/core';
import Tus from '@uppy/tus';
import Dashboard from '@uppy/dashboard';

// Import Uppy's CSS for the styling
// import '@uppy/core/style.css';
// import '@uppy/dashboard/style.css';

// Image upload logic for image_upload.blade.php
document.addEventListener('DOMContentLoaded', () => {
	const dragDropArea = document.getElementById('drag-drop-area');
	if (!dragDropArea) return;

	// Get product SKU from a data attribute or global JS variable
	let productSku = 'DUMMY-SKU-123';
	if (window.productSku) {
		productSku = window.productSku;
	} else {
		// Try to get from blade if rendered as a JS variable
		const skuMeta = document.querySelector('meta[name="product-sku"]');
		if (skuMeta) productSku = skuMeta.content;
	}

	const uppy = new Uppy({
		id: 'imageUploader',
		autoProceed: false,
		restrictions: {
			maxNumberOfFiles: 1,
			allowedFileTypes: ['image/*']
		},
		meta: {
			sku: productSku
		}
	});

	uppy.use(Dashboard, {
		target: '#drag-drop-area',
		inline: true,
		height: 350,
		showProgressDetails: true,
		proudlyDisplayPoweredByUppy: false,
		theme: 'light',
	});

	uppy.use(Tus, {
		endpoint: '/tus',
		chunkSize: 1024 * 1024 * 5,
		resume: true,
		retryDelays: [0, 1000, 3000, 5000],
		headers: {
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
		},
	});

	uppy.on('complete', (result) => {
		const statusArea = document.getElementById('status-area');
		statusArea.innerHTML = '';
		if (result.successful.length > 0) {
			const file = result.successful[0];
			const uploadUrl = file.uploadURL;
			statusArea.innerHTML = `<div class="status-message success">Upload complete! Processing variants...</div>`;
			window.axios.post(`/api/v1/products/${productSku}/image/link`, {
				upload_url: uploadUrl,
			}, {
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
				}
			})
			.then(response => {
				statusArea.innerHTML = `<div class="status-message success">✅ Image successfully linked and variants started! (Upload ID: ${response.data.upload_id})</div>`;
			})
			.catch(error => {
				const message = error.response ? error.response.data.message : 'Unknown linking error.';
				statusArea.innerHTML = `<div class="status-message error">❌ Linking Error: ${message}</div>`;
				console.error('Final Linking Error:', error);
			});
		} else {
			statusArea.innerHTML = `<div class="status-message error">❌ Upload Failed. Check console for details.</div>`;
		}
	});

	uppy.on('upload-error', (file, error, response) => {
		document.getElementById('status-area').innerHTML = `<div class="status-message error">❌ Upload Failed for ${file.name}: ${error.message}</div>`;
		console.error('Upload error:', file.name, error.message, response);
	});
});