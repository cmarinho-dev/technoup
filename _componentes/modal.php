<?php
function getModal($modal_content, $modal_title = 'Modal', $modal_id = '', $modal_show_oncreate = true, $modal_action = ''): string
{
    $show = $modal_show_oncreate ? '' : 'hidden';
  return <<<HTML
          <div id="$modal_id" class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4 $show" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
              <div class="mb-5 flex items-start justify-between">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-900 sm:text-2xl">$modal_title</h2>
                <button type="button" class="modal-close -me-4 -mt-4 rounded-full p-2 text-gray-400 transition-colors hover:bg-gray-50 hover:text-gray-600 focus:outline-none" aria-label="Close">
                  <i data-lucide="x" class="size-5"></i>
                </button>
              </div>
              <form action="$modal_action" method="POST" class="">
                <div class="space-y-6 py-3 px-2 pe-4 max-h-86 overflow-y-scroll">
                  $modal_content
                </div>
                <div class="mt-6 flex justify-end gap-2">
                  <button type="button" class="modal-close rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200">
                    Cancelar
                  </button>
                  <button type="submit" class="modal-save rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                    Salvar
                  </button>
                </div>
              </form>
            </div>
          </div>
          <script>
          document.addEventListener('DOMContentLoaded', function() {
            const modal = document.querySelectorAll('[role="dialog"]');
            const closeButtons = document.querySelectorAll('.modal-close');
            const saveButton = document.querySelector('.modal-save');

            function fecharModal() {
                modal.forEach((element) => {
                    element.style.display = 'none';
                })
            }
            function abrirModal() {
              modal.forEach((element) => {
                  if (element.id === 'modal_criar') {
                      element.style.display = 'grid';
                  }
              })
            }
            closeButtons.forEach(button => {
              button.addEventListener('click', fecharModal);
            });
            modal.addEventListener('click', function(event) {
              if (event.target === modal) {
                fecharModal();
              }
            });
            document.addEventListener('keydown', function(event) {
              if (event.key === 'Escape') {
                fecharModal();
              }
            });
          });
          </script>
HTML;
}