import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// //! handle select all checkboxes in the table
// document.addEventListener('DOMContentLoaded', () => {
//     const selectAllCheckbox = document.querySelector('thead input[type="checkbox"]');
//     const itemCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    
//     selectAllCheckbox?.addEventListener('change', () => {
//         itemCheckboxes.forEach(checkbox => {
//             checkbox.checked = selectAllCheckbox.checked;
//         });
//     });
    
//     itemCheckboxes.forEach(checkbox => {
//         checkbox.addEventListener('change', () => {
//             selectAllCheckbox.checked = Array.from(itemCheckboxes).every(cb => cb.checked);
//         });
//     });
// });
