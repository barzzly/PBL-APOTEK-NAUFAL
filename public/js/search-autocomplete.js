document.addEventListener('DOMContentLoaded', () => {
    const searchInputs = document.querySelectorAll('.header-search-input');
    
    searchInputs.forEach(input => {
        const container = input.closest('.relative');
        if (!container) return;
        
        // Create dropdown element
        const dropdown = document.createElement('div');
        dropdown.className = 'absolute left-0 right-0 mt-2 max-h-80 overflow-y-auto bg-white border border-gray-100 rounded-2xl shadow-xl z-50 hidden py-2 text-xs divide-y divide-gray-50';
        dropdown.style.top = '100%';
        container.appendChild(dropdown);
        
        let debounceTimer;
        
        input.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                dropdown.innerHTML = '';
                dropdown.classList.add('hidden');
                return;
            }
            
            // Show loading spinner
            dropdown.innerHTML = `
                <div class="px-4 py-4 text-center text-gray-400 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin text-primary text-sm"></i>
                    <span>Mencari obat...</span>
                </div>
            `;
            dropdown.classList.remove('hidden');
            
            debounceTimer = setTimeout(() => {
                fetch(`/search-suggestions?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        
                        if (data.length === 0) {
                            dropdown.innerHTML = `
                                <div class="px-4 py-4 text-center text-gray-400 italic">
                                    Obat "${escapeHtml(query)}" tidak ditemukan
                                </div>
                            `;
                            return;
                        }
                        
                        data.forEach(med => {
                            const item = document.createElement('a');
                            item.href = `/obat/${med.slug}`;
                            item.className = 'flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition text-left no-underline';
                            
                            let imgHtml = '';
                            if (med.image) {
                                const imgPath = med.image.startsWith('/') ? med.image : '/' + med.image;
                                imgHtml = `<img src="${imgPath}" alt="${med.name}" class="w-10 h-10 object-cover rounded-lg border border-gray-100 shrink-0">`;
                            } else {
                                imgHtml = `<div class="w-10 h-10 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center border border-gray-100 shrink-0"><i class="fa-solid fa-pills text-lg"></i></div>`;
                            }
                            
                            item.innerHTML = `
                                ${imgHtml}
                                <div class="min-w-0 flex-grow text-left">
                                    <div class="font-bold text-gray-700 text-sm truncate">${escapeHtml(med.name)}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">Kategori: ${escapeHtml(med.category_name || 'Umum')}</div>
                                </div>
                                <div class="font-extrabold text-secondary shrink-0 text-right text-xs">Rp ${med.price}</div>
                            `;
                            
                            dropdown.appendChild(item);
                        });
                    })
                    .catch(err => {
                        console.error('Error fetching search results:', err);
                        dropdown.innerHTML = `
                            <div class="px-4 py-4 text-center text-red-500 italic">
                                Gagal mengambil hasil pencarian.
                            </div>
                        `;
                    });
            }, 300);
        });
        
        // Hide dropdown on click outside
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Show dropdown if input has value and is focused
        input.addEventListener('focus', () => {
            if (input.value.trim().length >= 2 && dropdown.children.length > 0) {
                dropdown.classList.remove('hidden');
            }
        });
    });
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
