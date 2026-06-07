

import Alpine from 'alpinejs';
import { DataTable } from 'simple-datatables';
import 'simple-datatables/dist/style.css';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('[data-datatable]').forEach((table) => {
		const searchInput = table.closest('.py-1')?.querySelector('[data-datatable-search-input]');
		const perPageSelect = table.closest('.py-1')?.querySelector('[data-datatable-perpage]');

		const initialPerPage = perPageSelect ? parseInt(perPageSelect.value, 10) : 9;

		const dataTable = new DataTable(table, {
			perPage: initialPerPage,
			perPageSelect: false,
			searchable: !searchInput,
			labels: { info: '' },
		});

		if (searchInput) {
			let debounceTimer = null;

			searchInput.addEventListener('input', () => {
				window.clearTimeout(debounceTimer);
				debounceTimer = window.setTimeout(() => {
					dataTable.search(searchInput.value);
				}, 150);
			});
		}

		if (perPageSelect) {
			perPageSelect.addEventListener('change', () => {
				dataTable.options.perPage = parseInt(perPageSelect.value, 10);
				dataTable.update();
			});
		}
	});

});
