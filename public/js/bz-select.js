/**
 * Blue Zone Enterprise Searchable Select Engine (BZ-Select)
 * Features:
 * - Real-time instantaneous search with Arabic letter normalization (tashkeel, alef, teh marbuta)
 * - Pure fixed portal architecture: popover floats over cards, modals, and scrolling tables
 * - Intelligent collision detection: auto-flips upward if near screen bottom
 * - Automatic dynamic observation (MutationObserver) for AJAX / dynamic table rows & node deletion
 * - Full 2-way reactivity with native selects, Alpine.js (x-model), and Livewire
 * - HTML5 validation & error highlight support (redirects focus to trigger without form blocking)
 * - Property hooks on .value, .selectedIndex, and .disabled for instantaneous script synchronization
 * - Dark Mode & Light Mode reactive transitions
 * - RTL & LTR full orientation support
 */

(function () {
  'use strict';

  if (window.BZSelect) return;

  // Normalized Arabic & English string for search
  function normalizeText(str) {
    if (!str) return '';
    return str
      .toString()
      .toLowerCase()
      .trim()
      // Remove Arabic diacritics (tashkeel)
      .replace(/[\u064B-\u065F\u0670]/g, '')
      // Normalize Arabic letters
      .replace(/[إأآا]/g, 'ا')
      .replace(/ة/g, 'ه')
      .replace(/ي/g, 'ي')
      .replace(/ى/g, 'ي')
      .replace(/ؤ/g, 'و')
      .replace(/ئ/g, 'ي')
      // Normalize punctuation / symbols
      .replace(/[-_.,\/#!$%\^&\*;:{}=\-_`~()]/g, ' ')
      // Normalize extra spaces
      .replace(/\s+/g, ' ');
  }

  // Active open dropdown tracker
  let activeDropdown = null;

  function closeActiveDropdown() {
    if (activeDropdown) {
      activeDropdown.close();
      activeDropdown = null;
    }
  }

  // Global listeners for outside click and window events
  document.addEventListener('click', function (e) {
    if (activeDropdown) {
      if (
        !activeDropdown.wrapper.contains(e.target) &&
        !activeDropdown.dropdownEl.contains(e.target)
      ) {
        closeActiveDropdown();
      }
    }
  });

  window.addEventListener('resize', function () {
    if (activeDropdown) activeDropdown.updatePosition();
  });

  window.addEventListener('scroll', function (e) {
    if (activeDropdown) {
      // Don't close if scrolling inside the options list itself
      if (activeDropdown.dropdownEl.contains(e.target)) return;
      activeDropdown.updatePosition();
    }
  }, true);

  document.addEventListener('keydown', function (e) {
    if (activeDropdown) {
      if (e.key === 'Escape') {
        e.preventDefault();
        const trigger = activeDropdown.trigger;
        closeActiveDropdown();
        if (trigger) trigger.focus();
      }
    }
  });

  /**
   * BZSelect Class
   */
  class BZSelect {
    constructor(selectEl, options = {}) {
      this.selectEl = selectEl;
      this.options = options;
      this.isOpen = false;
      this.highlightedIndex = -1;
      this.items = [];

      if (this.selectEl.dataset.bzInitialized === 'true') return;
      this.selectEl.dataset.bzInitialized = 'true';

      this.init();
    }

    init() {
      // Hide native select visually while keeping it fully layout-active for HTML5 validation
      this.selectEl.classList.add('bz-hidden-select');
      this.selectEl.setAttribute('aria-hidden', 'true');

      // Create wrapper
      this.wrapper = document.createElement('div');
      this.wrapper.className = 'bz-select-wrapper';

      // Copy layout, sizing, and grid/flex classes from native select to wrapper
      Array.from(this.selectEl.classList).forEach(c => {
        if (
          c.startsWith('w-') ||
          c.startsWith('min-w-') ||
          c.startsWith('max-w-') ||
          c.startsWith('flex-') ||
          c.startsWith('col-span-')
        ) {
          this.wrapper.classList.add(c);
        }
      });

      // Preserve original inline width if set
      if (this.selectEl.style.width) {
        this.wrapper.style.width = this.selectEl.style.width;
      }

      // Detect size from classes
      let sizeClass = '';
      if (
        this.selectEl.classList.contains('text-xs') ||
        this.selectEl.classList.contains('form-select-sm') ||
        this.selectEl.classList.contains('btn-xs')
      ) {
        sizeClass = 'bz-size-xs';
      } else if (this.selectEl.classList.contains('text-sm')) {
        sizeClass = 'bz-size-sm';
      } else if (
        this.selectEl.classList.contains('text-lg') ||
        this.selectEl.classList.contains('form-select-lg')
      ) {
        sizeClass = 'bz-size-lg';
      }

      // Error state inheritance
      const hasError =
        this.selectEl.classList.contains('border-rose-500') ||
        this.selectEl.classList.contains('is-invalid') ||
        this.selectEl.classList.contains('border-danger');

      // Create trigger
      this.trigger = document.createElement('div');
      this.trigger.className = `bz-select-trigger ${sizeClass} ${hasError ? 'has-error' : ''} ${
        this.selectEl.disabled ? 'disabled' : ''
      }`;
      this.trigger.setAttribute('tabindex', this.selectEl.disabled ? '-1' : '0');
      this.trigger.setAttribute('role', 'combobox');
      this.trigger.setAttribute('aria-expanded', 'false');

      // Content container
      this.labelWrap = document.createElement('div');
      this.labelWrap.className = 'bz-select-label-wrap';

      this.labelEl = document.createElement('span');
      this.labelEl.className = 'bz-select-label';
      this.labelWrap.appendChild(this.labelEl);
      this.trigger.appendChild(this.labelWrap);

      // Actions container (chevron)
      this.actionsWrap = document.createElement('div');
      this.actionsWrap.className = 'bz-select-actions';

      this.chevron = document.createElement('span');
      this.chevron.className = 'bz-select-chevron';
      this.chevron.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
      this.actionsWrap.appendChild(this.chevron);
      this.trigger.appendChild(this.actionsWrap);

      // Insert wrapper in DOM next to native select
      if (this.selectEl.parentNode) {
        this.selectEl.parentNode.insertBefore(this.wrapper, this.selectEl);
      }
      this.wrapper.appendChild(this.selectEl);
      this.wrapper.appendChild(this.trigger);

      // Create Dropdown Portal element (will be attached to body when opened)
      this.dropdownEl = document.createElement('div');
      this.dropdownEl.className = 'bz-select-dropdown';

      // Search Wrap
      const isArabic =
        document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
      this.searchWrap = document.createElement('div');
      this.searchWrap.className = 'bz-select-search-wrap';

      const searchIcon = document.createElement('span');
      searchIcon.className = 'bz-select-search-icon';
      searchIcon.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i>';

      this.searchInput = document.createElement('input');
      this.searchInput.type = 'text';
      this.searchInput.className = 'bz-select-search-input';
      this.searchInput.placeholder = isArabic ? 'بحث سريع...' : 'Search options...';
      this.searchInput.setAttribute('autocomplete', 'off');
      this.searchInput.setAttribute('spellcheck', 'false');

      this.searchClear = document.createElement('span');
      this.searchClear.className = 'bz-select-search-clear hidden';
      this.searchClear.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';

      this.searchWrap.appendChild(searchIcon);
      this.searchWrap.appendChild(this.searchInput);
      this.searchWrap.appendChild(this.searchClear);
      this.dropdownEl.appendChild(this.searchWrap);

      // Options container
      this.optionsList = document.createElement('div');
      this.optionsList.className = 'bz-select-options';
      this.dropdownEl.appendChild(this.optionsList);

      // Attach to body for floating portal
      if (document.body) {
        document.body.appendChild(this.dropdownEl);
      } else {
        document.addEventListener('DOMContentLoaded', () => {
          document.body.appendChild(this.dropdownEl);
        });
      }

      // Bind Events & Hooks
      this.bindEvents();
      this.hookProperties();

      // Initial Sync
      this.syncFromNative();

      // Expose reference on DOM element
      this.selectEl.bzSelect = this;
    }

    hookProperties() {
      const self = this;

      // Hook .value property for instantaneous script synchronization (Alpine, jQuery, plain JS)
      const valDesc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
      if (valDesc && valDesc.set) {
        const origValSet = valDesc.set;
        const origValGet = valDesc.get;
        Object.defineProperty(this.selectEl, 'value', {
          get() {
            return origValGet.call(this);
          },
          set(val) {
            origValSet.call(this, val);
            self.syncFromNative();
          },
          configurable: true
        });
      }

      // Hook .selectedIndex property
      const selIdxDesc = Object.getOwnPropertyDescriptor(
        HTMLSelectElement.prototype,
        'selectedIndex'
      );
      if (selIdxDesc && selIdxDesc.set) {
        const origSetIdx = selIdxDesc.set;
        const origGetIdx = selIdxDesc.get;
        Object.defineProperty(this.selectEl, 'selectedIndex', {
          get() {
            return origGetIdx.call(this);
          },
          set(val) {
            origSetIdx.call(this, val);
            self.syncFromNative();
          },
          configurable: true
        });
      }

      // Hook .disabled property
      const disDesc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'disabled');
      if (disDesc && disDesc.set) {
        const origSetDis = disDesc.set;
        const origGetDis = disDesc.get;
        Object.defineProperty(this.selectEl, 'disabled', {
          get() {
            return origGetDis.call(this);
          },
          set(val) {
            origSetDis.call(this, val);
            self.syncFromNative();
          },
          configurable: true
        });
      }
    }

    bindEvents() {
      // Toggle on click
      this.trigger.addEventListener('click', (e) => {
        if (this.selectEl.disabled) return;
        e.preventDefault();
        e.stopPropagation();
        this.toggle();
      });

      // Keyboard navigation on trigger
      this.trigger.addEventListener('keydown', (e) => {
        if (this.selectEl.disabled) return;
        if (['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(e.key)) {
          e.preventDefault();
          if (!this.isOpen) {
            this.open();
          } else if (e.key === 'ArrowDown') {
            this.moveHighlight(1);
          } else if (e.key === 'ArrowUp') {
            this.moveHighlight(-1);
          } else if (['Enter', ' '].includes(e.key) && this.highlightedIndex >= 0) {
            this.selectItemByIndex(this.highlightedIndex);
          }
        }
      });

      // Search input filtering
      this.searchInput.addEventListener('input', () => {
        const query = this.searchInput.value;
        if (query.trim().length > 0) {
          this.searchClear.classList.remove('hidden');
        } else {
          this.searchClear.classList.add('hidden');
        }
        this.filterOptions(query);
      });

      // Clear search button
      this.searchClear.addEventListener('click', (e) => {
        e.stopPropagation();
        this.searchInput.value = '';
        this.searchClear.classList.add('hidden');
        this.filterOptions('');
        this.searchInput.focus();
      });

      // Keyboard navigation on search input
      this.searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          this.moveHighlight(1);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          this.moveHighlight(-1);
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (this.highlightedIndex >= 0) {
            this.selectItemByIndex(this.highlightedIndex);
          } else {
            const firstVisible = this.items.find(
              i => !i.element.classList.contains('hidden') && !i.disabled
            );
            if (firstVisible) this.selectItem(firstVisible);
          }
        } else if (e.key === 'Escape') {
          e.preventDefault();
          this.close();
          this.trigger.focus();
        } else if (e.key === 'Tab') {
          this.close();
        }
      });

      // Listen for changes on native select
      this.selectEl.addEventListener('change', () => {
        this.syncFromNative();
      });

      // HTML5 Validation: focus redirect and error styling
      this.selectEl.addEventListener('focus', () => {
        this.trigger.focus();
      });

      this.selectEl.addEventListener('invalid', () => {
        this.trigger.classList.add('has-error');
        this.trigger.focus();
      });

      // Observe dynamic changes in <option> tags & attributes
      this.mutationObserver = new MutationObserver(() => {
        this.syncFromNative();
      });
      this.mutationObserver.observe(this.selectEl, {
        childList: true,
        subtree: true,
        attributes: true
      });
    }

    renderOptions() {
      this.optionsList.innerHTML = '';
      this.items = [];

      const options = Array.from(this.selectEl.children);
      const isArabic =
        document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

      options.forEach((child) => {
        if (child.tagName === 'OPTGROUP') {
          const groupHeader = document.createElement('div');
          groupHeader.className = 'bz-select-optgroup-label';
          groupHeader.textContent = child.label || '';
          this.optionsList.appendChild(groupHeader);

          const groupOptions = Array.from(child.children);
          groupOptions.forEach((opt) => {
            this.createOptionElement(opt, groupHeader);
          });
        } else if (child.tagName === 'OPTION') {
          this.createOptionElement(child, null);
        }
      });

      // Show/Hide search wrap:
      // Show search bar if options >= 4 or explicitly requested via data-search
      const totalOptions = this.items.length;
      const forceSearch = this.selectEl.dataset.search === 'true';
      const noSearch =
        this.selectEl.dataset.noSearch === 'true' ||
        this.selectEl.classList.contains('no-search-select');

      if (noSearch || (totalOptions < 4 && !forceSearch)) {
        this.searchWrap.style.display = 'none';
      } else {
        this.searchWrap.style.display = 'block';
      }

      // Empty state element
      if (!this.emptyEl) {
        this.emptyEl = document.createElement('div');
        this.emptyEl.className = 'bz-select-empty hidden';
        this.emptyEl.innerHTML = `<i class="fa-solid fa-magnifying-glass text-xs mr-1 ml-1 opacity-50"></i> ${
          isArabic ? 'لا توجد نتائج مطابقة' : 'No matching options found'
        }`;
      }
      this.optionsList.appendChild(this.emptyEl);
    }

    createOptionElement(optionEl, parentGroupHeader = null) {
      const isSelected = optionEl.selected;
      const isDisabled = optionEl.disabled;
      const text = optionEl.textContent.trim();
      const value = optionEl.value;

      const itemDiv = document.createElement('div');
      itemDiv.className = `bz-select-option ${isSelected ? 'is-selected' : ''} ${
        isDisabled ? 'is-disabled' : ''
      }`;
      itemDiv.setAttribute('role', 'option');
      itemDiv.setAttribute('aria-selected', isSelected ? 'true' : 'false');
      itemDiv.dataset.value = value;

      const textSpan = document.createElement('span');
      textSpan.className = 'bz-select-option-text';
      textSpan.textContent = text;
      itemDiv.appendChild(textSpan);

      if (isSelected) {
        const checkSpan = document.createElement('span');
        checkSpan.className = 'bz-select-option-check';
        checkSpan.innerHTML = '<i class="fa-solid fa-check"></i>';
        itemDiv.appendChild(checkSpan);
      }

      // Comprehensive search corpus: text + value + data attributes (name, code, sku, symbol)
      const searchCorpus = [
        text,
        value,
        optionEl.getAttribute('data-name'),
        optionEl.getAttribute('data-code'),
        optionEl.getAttribute('data-sku'),
        optionEl.getAttribute('data-symbol')
      ]
        .filter(Boolean)
        .join(' ');

      const itemObj = {
        element: itemDiv,
        optionEl: optionEl,
        text: text,
        value: value,
        normalized: normalizeText(searchCorpus),
        disabled: isDisabled,
        groupHeader: parentGroupHeader
      };

      itemDiv.addEventListener('click', (e) => {
        e.stopPropagation();
        if (isDisabled) return;
        this.selectItem(itemObj);
      });

      this.items.push(itemObj);
      this.optionsList.appendChild(itemDiv);
    }

    selectItem(itemObj) {
      this.selectEl.value = itemObj.value;

      // Update native option selected properties
      Array.from(this.selectEl.options).forEach((opt) => {
        opt.selected = opt === itemObj.optionEl;
      });

      // Clear any invalid error styling on select
      this.trigger.classList.remove('has-error');

      // Dispatch native change and input events with full bubbling
      this.selectEl.dispatchEvent(new Event('change', { bubbles: true }));
      this.selectEl.dispatchEvent(new Event('input', { bubbles: true }));

      this.syncFromNative();
      this.close();
      this.trigger.focus();
    }

    selectItemByIndex(index) {
      if (index >= 0 && index < this.items.length) {
        const item = this.items[index];
        if (!item.disabled) {
          this.selectItem(item);
        }
      }
    }

    syncFromNative() {
      // Re-read selected option from native select
      const selectedOption = this.selectEl.selectedOptions[0] || this.selectEl.options[0];

      if (selectedOption) {
        const text = selectedOption.textContent.trim();
        this.labelEl.textContent = text;

        if (!selectedOption.value || selectedOption.value === '') {
          this.labelEl.classList.add('is-placeholder');
        } else {
          this.labelEl.classList.remove('is-placeholder');
        }
      } else {
        this.labelEl.textContent = '--';
        this.labelEl.classList.add('is-placeholder');
      }

      // Sync disabled state
      if (this.selectEl.disabled) {
        this.trigger.classList.add('disabled');
        this.trigger.setAttribute('tabindex', '-1');
      } else {
        this.trigger.classList.remove('disabled');
        this.trigger.setAttribute('tabindex', '0');
      }

      // Re-render items in list
      this.renderOptions();
    }

    filterOptions(query) {
      const q = normalizeText(query);
      let visibleCount = 0;
      const groupVisibility = new Map();

      this.items.forEach((item) => {
        const matches = !q || item.normalized.includes(q);
        if (matches) {
          item.element.classList.remove('hidden');
          visibleCount++;
          if (item.groupHeader) {
            groupVisibility.set(item.groupHeader, true);
          }
        } else {
          item.element.classList.add('hidden');
        }
        item.element.classList.remove('is-highlighted');
      });

      // Update optgroup headers visibility
      this.optionsList.querySelectorAll('.bz-select-optgroup-label').forEach((header) => {
        if (groupVisibility.get(header) || !q) {
          header.style.display = 'block';
        } else {
          header.style.display = 'none';
        }
      });

      // Empty state
      if (visibleCount === 0) {
        this.emptyEl.classList.remove('hidden');
      } else {
        this.emptyEl.classList.add('hidden');
      }

      this.highlightedIndex = -1;
    }

    moveHighlight(delta) {
      const visibleItems = this.items.filter(
        (i) => !i.element.classList.contains('hidden') && !i.disabled
      );
      if (visibleItems.length === 0) return;

      let currentIdx = visibleItems.findIndex((i) =>
        i.element.classList.contains('is-highlighted')
      );
      if (currentIdx === -1) {
        currentIdx = delta > 0 ? -1 : 0;
      }

      let newIdx = currentIdx + delta;
      if (newIdx < 0) newIdx = visibleItems.length - 1;
      if (newIdx >= visibleItems.length) newIdx = 0;

      visibleItems.forEach((i, idx) => {
        if (idx === newIdx) {
          i.element.classList.add('is-highlighted');
          i.element.scrollIntoView({ block: 'nearest' });
          this.highlightedIndex = this.items.indexOf(i);
        } else {
          i.element.classList.remove('is-highlighted');
        }
      });
    }

    open() {
      if (this.isOpen || this.selectEl.disabled) return;

      closeActiveDropdown();

      this.isOpen = true;
      activeDropdown = this;

      this.wrapper.classList.add('is-open');
      this.trigger.setAttribute('aria-expanded', 'true');

      // Refresh options list & search input
      this.renderOptions();
      this.searchInput.value = '';
      this.searchClear.classList.add('hidden');
      this.filterOptions('');

      // Show dropdown & calculate position
      this.dropdownEl.classList.add('is-active');
      this.updatePosition();

      // Focus search input
      if (this.searchWrap.style.display !== 'none') {
        setTimeout(() => {
          this.searchInput.focus();
        }, 30);
      }
    }

    close() {
      if (!this.isOpen) return;

      this.isOpen = false;
      if (activeDropdown === this) {
        activeDropdown = null;
      }

      this.wrapper.classList.remove('is-open');
      this.trigger.setAttribute('aria-expanded', 'false');
      this.dropdownEl.classList.remove('is-active');
      this.searchInput.blur();
    }

    toggle() {
      this.isOpen ? this.close() : this.open();
    }

    updatePosition() {
      if (!this.isOpen) return;

      const triggerRect = this.trigger.getBoundingClientRect();

      // If trigger is hidden (e.g. modal closed or tab changed), close dropdown
      if (triggerRect.width === 0 && triggerRect.height === 0) {
        this.close();
        return;
      }

      const dropdownRect = this.dropdownEl.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      const viewportWidth = window.innerWidth;

      const minWidth = Math.max(triggerRect.width, 220);
      this.dropdownEl.style.minWidth = `${minWidth}px`;
      this.dropdownEl.style.maxWidth = `${Math.min(viewportWidth - 20, 560)}px`;

      // Horizontal Positioning with safety margin
      let left = triggerRect.left;
      if (left + minWidth > viewportWidth - 10) {
        left = Math.max(10, viewportWidth - minWidth - 10);
      }
      this.dropdownEl.style.left = `${Math.max(10, left)}px`;

      // Vertical Positioning with Collision Detection
      const dropdownHeight = dropdownRect.height || 280;
      const spaceBelow = viewportHeight - triggerRect.bottom;
      const spaceAbove = triggerRect.top;

      if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
        // Flip Upward
        this.dropdownEl.classList.add('open-up');
        this.dropdownEl.style.top = `${triggerRect.top - dropdownHeight - 6}px`;
      } else {
        // Open Downward
        this.dropdownEl.classList.remove('open-up');
        this.dropdownEl.style.top = `${triggerRect.bottom + 6}px`;
      }
    }

    destroy() {
      if (this.mutationObserver) this.mutationObserver.disconnect();
      if (this.dropdownEl && this.dropdownEl.parentNode) {
        this.dropdownEl.parentNode.removeChild(this.dropdownEl);
      }
      if (this.trigger && this.trigger.parentNode) {
        this.trigger.parentNode.removeChild(this.trigger);
      }
      this.selectEl.classList.remove('bz-hidden-select');
      delete this.selectEl.dataset.bzInitialized;
      delete this.selectEl.bzSelect;
    }
  }

  // Global Engine Controller
  window.BZSelect = {
    init: function (root = document) {
      if (!root) return;
      const selects = root.querySelectorAll(
        'select:not([multiple]):not([data-bz-ignore="true"]):not(.bz-ignore):not(.bz-hidden-select):not([data-bz-initialized="true"])'
      );

      selects.forEach((select) => {
        // Skip Filament internal elements
        if (select.closest('.fi-ta-content') || select.closest('.fi-input-wrp')) {
          return;
        }
        new BZSelect(select);
      });
    },

    enhance: function (selectEl, options = {}) {
      if (!selectEl) return null;
      if (selectEl.bzSelect) return selectEl.bzSelect;
      return new BZSelect(selectEl, options);
    },

    syncAll: function () {
      document.querySelectorAll('select.bz-hidden-select').forEach((sel) => {
        if (sel.bzSelect) sel.bzSelect.syncFromNative();
      });
    }
  };

  // Dynamic MutationObserver to catch dynamically added select elements (like new rows, modals, ajax)
  // and clean up destroyed elements when rows are removed
  function observeDynamicSelects() {
    if (!document.body) return;

    const observer = new MutationObserver((mutations) => {
      let shouldScan = false;

      for (const m of mutations) {
        // Handle added nodes
        if (m.addedNodes && m.addedNodes.length > 0) {
          for (const node of m.addedNodes) {
            if (node.nodeType === 1) {
              if (node.tagName === 'SELECT' || node.querySelector('select')) {
                shouldScan = true;
                break;
              }
            }
          }
        }

        // Handle removed nodes to prevent detached portal leaks
        if (m.removedNodes && m.removedNodes.length > 0) {
          for (const node of m.removedNodes) {
            if (node.nodeType === 1) {
              const removedSelects =
                node.tagName === 'SELECT' ? [node] : Array.from(node.querySelectorAll('select'));
              removedSelects.forEach((s) => {
                if (s.bzSelect) {
                  s.bzSelect.destroy();
                }
              });
            }
          }
        }

        if (shouldScan) break;
      }

      if (shouldScan) {
        window.BZSelect.init();
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }

  // Auto-init on DOMContentLoaded or immediately if DOM is already ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      window.BZSelect.init();
      observeDynamicSelects();
    });
  } else {
    window.BZSelect.init();
    observeDynamicSelects();
  }
})();
