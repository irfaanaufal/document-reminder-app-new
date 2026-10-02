import Alpine from 'alpinejs';
import { DataTable } from 'simple-datatables';
import 'simple-datatables/dist/style.css';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-datatable]').forEach((table) => {
        const searchInput = document.querySelector('[data-datatable-search-input]');

        const dataTable = new DataTable(table, {
            perPage: 25,
            perPageSelect: false,
            searchable: false,
            labels: { info: '' },
        });

        if (searchInput) {
            let debounceTimer = null;
            searchInput.addEventListener('input', () => {
                window.clearTimeout(debounceTimer);
                debounceTimer = window.setTimeout(() => {
                    const value = searchInput.value.trim().toLowerCase();
                    if (value) {
                        dataTable.search(value);
                    } else {
                        dataTable.search('');
                    }
                    document.querySelectorAll('[data-doc-search]').forEach((el) => {
                        if (!value) {
                            el.classList.remove('hidden');
                            return;
                        }
                        const hay = (el.getAttribute('data-doc-search') || '').toLowerCase();
                        el.classList.toggle('hidden', !hay.includes(value));
                    });
                }, 150);
            });
        }
    });
});
