/**
 * Admin Panel JS
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
  // Sidebar toggle (mobile)
  const menuBtn = document.getElementById('admin-menu-toggle');
  const sidebar = document.querySelector('.admin-sidebar');
  menuBtn?.addEventListener('click', () => sidebar?.classList.toggle('open'));

  // Image preview
  const imageInput = document.getElementById('project-images');
  const preview    = document.getElementById('image-preview');
  imageInput?.addEventListener('change', () => {
    preview.innerHTML = '';
    [...imageInput.files].forEach(f => {
      if (!f.type.match('image.*')) return;
      const reader = new FileReader();
      reader.onload = e => {
        const wrap = document.createElement('div');
        wrap.style.cssText = 'position:relative;display:inline-block;';
        const img  = document.createElement('img');
        img.src    = e.target.result;
        img.style.cssText = 'width:100px;height:75px;object-fit:cover;border-radius:8px;border:1px solid var(--border)';
        wrap.appendChild(img);
        preview.appendChild(wrap);
      };
      reader.readAsDataURL(f);
    });
  });

  // Confirm delete
  document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) e.preventDefault();
    });
  });

  // Auto-hide alerts
  document.querySelectorAll('.alert-auto').forEach(a => {
    setTimeout(() => a.style.opacity = '0', 3000);
    setTimeout(() => a.remove(), 3500);
  });

  // Character counter for textarea
  const ta    = document.querySelector('textarea[data-maxlen]');
  const count = document.getElementById('char-count');
  if (ta && count) {
    count.textContent = ta.value.length;
    ta.addEventListener('input', () => { count.textContent = ta.value.length; });
  }
});
