import './bootstrap';

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;


import 'summernote/dist/summernote-lite.js';
import 'summernote/dist/summernote-lite.css';


document.addEventListener('DOMContentLoaded', function() {
  // Certifique-se de que o seletor #texto aponte para o seu <textarea>
  $('#texto').summernote({
    placeholder: 'Digite o conteúdo da sua postagem aqui...',
    tabsize: 2,
    height: 300, // Altura do editor
    // Desativa a funcionalidade de arrastar e soltar (drag-and-drop)
    disableDragAndDrop: true, // <--- Adicione esta linha!

    fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana'],
    fontNamesIgnoreCheck: ['Arial Black', 'Comic Sans MS', 'Lucida Grande', 'Times New Roman'],

    toolbar: [
      ['style', ['style', 'bold', 'italic', 'underline']],
      ['font', ['strikethrough', 'superscript', 'subscript', 'bold', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['table', ['table']],
      ['insert', ['link', 'picture', 'video']],
      ['view', ['codeview', 'help']],
    ]
  });
});

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
