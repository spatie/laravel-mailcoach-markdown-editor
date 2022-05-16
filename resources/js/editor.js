import {$, jsonFetch} from './util';
import {showModal} from './components/modal';

document.addEventListener('turbo:load', initEditor);
document.addEventListener('load', initEditor);
document.addEventListener('before-visit', confirmBeforeLeaveAndDestroyEditor);
window.addEventListener('beforeunload', confirmBeforeLeaveAndDestroyEditor);

initEditor();

function confirmBeforeLeaveAndDestroyEditor(event) {
    if (! document.getElementById('html')) {
        return;
    }

    if (document.getElementById('html').dataset.dirty === "dirty" && ! confirm('Are you sure you want to leave this page? Any unsaved changes will be lost.')) {
        event.preventDefault();
        return;
    }

    document.removeEventListener('turbo:before-visit', confirmBeforeLeaveAndDestroyEditor);
    window.removeEventListener('beforeunload', confirmBeforeLeaveAndDestroyEditor);
    window.editor.destroy();
    window.editor = undefined;
}

function initEditor() {
    document.addEventListener('turbo:before-visit', confirmBeforeLeaveAndDestroyEditor);
    document.addEventListener("turbo:load", initEditor);
    window.addEventListener('beforeunload', confirmBeforeLeaveAndDestroyEditor);

    const el = $('#markdown-editor');
    if (! el || window.editor !== undefined) {
        return;
    }

    const { Editor } = toastui;
    const { codeSyntaxHighlight, tableMergedCell, colorSyntax } = Editor.plugin;

    const options = JSON.parse(el.dataset.options);
    window.editor = new Editor({...options,
        el: el,
        plugins: [
            codeSyntaxHighlight,
            tableMergedCell,
            colorSyntax,
        ],
    });
    window.editor.setMarkdown(el.dataset.structuredHtml || '');


    window.editor.addHook('change', () => {
        document.getElementById('html').dataset.dirty = "dirty";
    });

    window.editor.addHook('addImageBlobHook', (blob, callback) => {
        const data = new FormData();
        data.append('file', blob);

        jsonFetch(el.dataset.upload, data).then(({ success, file }) => {
            if (! success) {
                return;
            }

            callback(file.url, 'alt text');
        });
    });

    function getHTML() {
        const template = $('#template').value;
        const html = editor.getHTML().replaceAll("<p><br></p>", '');
        return template.replace('::content::', html);
    }

    $('#save').addEventListener('click', (event) => {
        event.preventDefault();

        document.getElementById('html').value = getHTML();
        document.getElementById('body').value = editor.getMarkdown();
        document.getElementById('html').dataset.dirty = "";
        document.querySelector('main form').submit();
    });

    $('#preview').addEventListener('click', (event) => {
        event.preventDefault();
        $('#html').value = getHTML();
        const input = document.createEvent('Event');
        input.initEvent('input', true, true);
        document.getElementById('html').dispatchEvent(input);
        showModal('preview');
    });

    $("[data-modal-trigger=\"edit-template\"]").addEventListener('click', () => {
        document.getElementById("structured_html[template]").value = $('#template').value;
    });

    $("[data-modal-confirm=\"edit-template\"]").addEventListener('click', () => {
        $('#template').value = document.getElementById("structured_html[template]").value;
    });
}
