import { Controller } from '@hotwired/stimulus';
import $ from 'jquery'
import 'datatables.net-bs5'
import 'datatables.net-buttons-bs5'
import 'datatables.net-fixedheader-bs5'
import 'datatables.net-responsive-bs5'
import 'datatables.net-scroller-bs5'
import 'datatables.net-searchbuilder-bs5'
import 'datatables.net-searchpanes-bs5'
import 'datatables.net-select-bs5'
export default class extends Controller {
    connect() {
        const tableElement = $(this.element).find('table');
        if (!tableElement.length || $.fn.DataTable.isDataTable(tableElement)) {
            return;
        }
        window.$ = window.jQuery = $;
        const sortIndex = this.element.dataset.orderIndex || 0;
        const sortDir = this.element.dataset.orderDir || 'asc';
        this.table = tableElement.DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.0/i18n/fr-FR.json',
            },
            order: [[sortIndex, sortDir]],
            lengthChange: false,
            info: false,
            responsive: true,
            colReorder: true,
            rowReorder: true,
            fixedHeader: true,
            select: true,
        })
    }
    disconnect()
    {
        if (this.table) {
            try {
                this.table.destroy(false);
            } catch (e) {
            }
            this.table = null;
        }
    }
}
