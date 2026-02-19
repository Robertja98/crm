/**
 * SIDEBAR NAVIGATION CONTROLS
 * Handles mobile menu toggle and sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  const menuToggle = document.getElementById('menuToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const debugClicks = new URLSearchParams(window.location.search).get('debugClick') === '1';
  const panelToggles = document.querySelectorAll('.js-toggle-panel');
  const queryInput = document.getElementById('query');
  
  // Toggle sidebar on mobile
  if (menuToggle) {
    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('open');
      sidebarOverlay.classList.toggle('active');
      
      // Update aria attributes for accessibility
      const isOpen = sidebar.classList.contains('open');
      sidebar.setAttribute('aria-hidden', !isOpen);
      menuToggle.setAttribute('aria-expanded', isOpen);
    });
  }
  
  // Close sidebar when clicking overlay
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('active');
    });
  }
  
  // Collapse sidebar on desktop (optional)
  const collapseSidebarBtn = document.getElementById('collapseSidebar');
  if (collapseSidebarBtn) {
    collapseSidebarBtn.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
      
      // Save preference
      const isCollapsed = sidebar.classList.contains('collapsed');
      localStorage.setItem('sidebarCollapsed', isCollapsed);
    });
  }
  
  // Restore sidebar state from localStorage
  const savedState = localStorage.getItem('sidebarCollapsed');
  if (savedState === 'true') {
    sidebar.classList.add('collapsed');
    mainContent.classList.add('expanded');
  }
  
  // Close mobile sidebar when clicking a link
  const navLinks = sidebar.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('active');
      }
    });
  });
  
  // Handle window resize
  let resizeTimeout;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
      // Close mobile sidebar if window is resized to desktop
      if (window.innerWidth > 768) {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('active');
      }
    }, 250);
  });
  
  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    // Escape key closes mobile sidebar
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('active');
    }
    
    // Ctrl/Cmd + B toggles sidebar (desktop)
    if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
      e.preventDefault();
      if (window.innerWidth > 768) {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
      }
    }
  });

  // Toggle panels (contacts list)
  if (panelToggles.length > 0) {
    panelToggles.forEach(btn => {
      btn.addEventListener('click', function() {
        const targetId = btn.getAttribute('data-target');
        if (!targetId) return;
        const panel = document.getElementById(targetId);
        if (!panel) return;
        const isHidden = panel.style.display === 'none' || panel.style.display === '';
        panel.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
          panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      });
    });
  }

  // Search focus shortcuts (contacts list)
  if (queryInput) {
    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey && e.key === 'f') || (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName))) {
        e.preventDefault();
        queryInput.focus();
      }
    });
  }

  // Optional click debug (enable with ?debugClick=1)
  if (debugClicks) {
    document.addEventListener('click', function(e) {
      const el = document.elementFromPoint(e.clientX, e.clientY);
      if (!el) return;
      const href = el.closest('a') ? el.closest('a').getAttribute('href') : '';
      const details = [
        `tag=${el.tagName.toLowerCase()}`,
        el.id ? `id=${el.id}` : '',
        el.className ? `class=${el.className}` : '',
        href ? `href=${href}` : ''
      ].filter(Boolean).join(' | ');

      alert(`Clicked element: ${details}`);

      el.style.outline = '2px solid #ef4444';
      setTimeout(() => {
        el.style.outline = '';
      }, 1500);
    }, true);
  }
});

/**
 * GLOBAL SEARCH FUNCTIONALITY
 * Real-time search across contacts, companies, etc.
 * DISABLED - Now uses form submission to contacts_list.php instead
 */

/*
let searchTimeout;
let searchCache = {};

document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('globalSearch');
  
  if (!searchInput) return;
  
  // Debounced search
  searchInput.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.trim();
    
    if (query.length < 2) {
      hideSearchResults();
      return;
    }
    
    searchTimeout = setTimeout(() => {
      performSearch(query);
    }, 300); // Wait 300ms after user stops typing
  });
  
  // Keyboard shortcuts for search
  document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K or / to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
    
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
      e.preventDefault();
      searchInput.focus();
    }
  });
  
  // Click outside to close search results
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-bar')) {
      hideSearchResults();
    }
  });
});
*/

/*
async function performSearch(query) {
  // Check cache first
  if (searchCache[query]) {
    displaySearchResults(searchCache[query]);
    return;
  }
  
  try {
    // Show loading state
    showSearchLoading();
    
    // Fetch search results from server
    const response = await fetch(`search.php?q=${encodeURIComponent(query)}`);
    const results = await response.json();
    
    // Cache results
    searchCache[query] = results;
    
    // Display results
    displaySearchResults(results);
    
  } catch (error) {
    console.error('Search error:', error);
    showSearchError();
  }
}

function displaySearchResults(results) {
  let resultsHTML = '<div class="search-results">';
  
  if (results.length === 0) {
    resultsHTML += '<div class="search-empty">No results found</div>';
  } else {
    // Group by type
    const grouped = {};
    results.forEach(item => {
      if (!grouped[item.type]) grouped[item.type] = [];
      grouped[item.type].push(item);
    });
    
    // Render each group
    for (const [type, items] of Object.entries(grouped)) {
      resultsHTML += `<div class="search-group">`;
      resultsHTML += `<div class="search-group-title">${type}</div>`;
      
      items.slice(0, 5).forEach(item => {
        resultsHTML += `
          <a href="${item.url}" class="search-result-item">
            <div class="search-result-title">${highlightMatch(item.title, query)}</div>
            <div class="search-result-subtitle">${item.subtitle || ''}</div>
          </a>
        `;
      });
      
      if (items.length > 5) {
        resultsHTML += `<div class="search-more">+${items.length - 5} more</div>`;
      }
      
      resultsHTML += `</div>`;
    }
  }
  
  resultsHTML += '</div>';
  
  // Insert or update results container
  let container = document.querySelector('.search-results');
  if (container) {
    container.outerHTML = resultsHTML;
  } else {
    document.querySelector('.search-bar').insertAdjacentHTML('beforeend', resultsHTML);
  }
}

function highlightMatch(text, query) {
  const regex = new RegExp(`(${query})`, 'gi');
  return text.replace(regex, '<mark>$1</mark>');
}

function showSearchLoading() {
  const html = `
    <div class="search-results">
      <div class="search-loading">
        <span class="loading-spinner"></span> Searching...
      </div>
    </div>
  `;
  
  const existing = document.querySelector('.search-results');
  if (existing) {
    existing.outerHTML = html;
  } else {
    document.querySelector('.search-bar').insertAdjacentHTML('beforeend', html);
  }
}

function showSearchError() {
  const html = `
    <div class="search-results">
      <div class="search-error">Search failed. Please try again.</div>
    </div>
  `;
  
  const existing = document.querySelector('.search-results');
  if (existing) existing.outerHTML = html;
}

function hideSearchResults() {
  const results = document.querySelector('.search-results');
  if (results) results.remove();
}
*/

/**
 * FORM LOADING STATES
 * Shows visual feedback when forms are submitting
 */

document.addEventListener('DOMContentLoaded', function() {
  const forms = document.querySelectorAll('form[method="POST"]');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      
      if (submitBtn && !submitBtn.disabled) {
        // Prevent double submission
        submitBtn.disabled = true;
        
        // Show loading state
        const originalText = submitBtn.textContent || submitBtn.value;
        submitBtn.dataset.originalText = originalText;
        
        if (submitBtn.tagName === 'BUTTON') {
          submitBtn.innerHTML = '<span class="loading-spinner"></span> Saving...';
        } else {
          submitBtn.value = 'Saving...';
        }
        
        // Re-enable after 10 seconds (safety)
        setTimeout(() => {
          submitBtn.disabled = false;
          if (submitBtn.tagName === 'BUTTON') {
            submitBtn.textContent = originalText;
          } else {
            submitBtn.value = originalText;
          }
        }, 10000);
      }
    });
  });
});

/**
 * TOAST NOTIFICATIONS
 * Non-intrusive success/error messages
 */

function showToast(message, type = 'success', duration = 3000) {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <div class="toast-content">
      <span class="toast-icon">${getToastIcon(type)}</span>
      <span class="toast-message">${message}</span>
    </div>
  `;
  
  // Add to container or body
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  
  container.appendChild(toast);
  
  // Trigger animation
  setTimeout(() => toast.classList.add('show'), 10);
  
  // Auto remove
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, duration);
  
  // Click to dismiss
  toast.addEventListener('click', () => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  });
}

function getToastIcon(type) {
  const icons = {
    success: '✓',
    error: '✗',
    warning: '⚠',
    info: 'ℹ'
  };
  return icons[type] || icons.info;
}

// CSS for toasts (add to modern-components.css)
const toastStyles = `
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 10000;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.toast {
  min-width: 300px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 16px;
  transform: translateX(400px);
  opacity: 0;
  transition: all 0.3s ease;
  border-left: 4px solid;
}

.toast.show {
  transform: translateX(0);
  opacity: 1;
}

.toast.toast-success { border-left-color: #10b981; }
.toast.toast-error { border-left-color: #ef4444; }
.toast.toast-warning { border-left-color: #f59e0b; }
.toast.toast-info { border-left-color: #3b82f6; }

.toast-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.toast-icon {
  font-size: 20px;
  font-weight: bold;
}

.toast.toast-success .toast-icon { color: #10b981; }
.toast.toast-error .toast-icon { color: #ef4444; }
.toast.toast-warning .toast-icon { color: #f59e0b; }
.toast.toast-info .toast-icon { color: #3b82f6; }
`;

// Usage examples:
// showToast('Contact saved successfully!', 'success');
// showToast('Failed to save contact', 'error');
// showToast('Email field is required', 'warning');
