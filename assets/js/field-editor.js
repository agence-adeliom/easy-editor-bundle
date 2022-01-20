import Sortable from 'sortablejs';


const eaEditorHandler = function (event) {
    document.querySelectorAll('button.field-editor-add-button:not(.processed)').forEach((addButton) => {
        const collection = addButton.closest('[data-ea-collection-field]');
        addButton.classList.add('processed');
        EaEditorCollectionProperty.handleAddButton(addButton, collection);
        EaEditorCollectionProperty.updateCollectionItemCssClasses(collection);
        EaEditorCollectionProperty.updateCollectionSortable(collection);
    });

    document.querySelectorAll('button.field-editor-remove-button').forEach((deleteButton) => {
        deleteButton.addEventListener('click', () => {
            const collection = deleteButton.closest('[data-ea-collection-field]');

            deleteButton.closest('.form-group').remove();
            document.dispatchEvent(new Event('ea.editor.item-removed'));

            EaEditorCollectionProperty.updateCollectionItemCssClasses(collection);
            EaEditorCollectionProperty.updateCollectionSortable(collection);
        });
    });

    if (typeof CKEDITOR !== 'undefined' && CKEDITOR && CKEDITOR.instances){
        for (const editorKey in CKEDITOR.instances) {
            var editor = CKEDITOR.instances[editorKey];
            editor.mode = 'source';
            editor.setMode('wysiwyg');
        }
    }


    if(event.type === "DOMContentLoaded"){
        document.dispatchEvent(new Event('ea.editor.item-loaded'));
    }


}

window.addEventListener('DOMContentLoaded', eaEditorHandler);
document.addEventListener('ea.editor.item-added', eaEditorHandler);
document.addEventListener('ea.collection.item-added', eaEditorHandler);

const EaEditorCollectionProperty = {
    handleAddButton: (addButton, collection) => {
        addButton.addEventListener('click', function() {
            const isArrayCollection = collection.classList.contains('field-array');
            // Use a counter to avoid having the same index more than once
            let numItems = parseInt(collection.dataset.numItems);

            // Remove the 'Empty Collection' badge, if present
            const emptyCollectionBadge = collection.querySelector('.collection-empty');
            if (null !== emptyCollectionBadge) {
                emptyCollectionBadge.outerHTML = isArrayCollection ? '<div class="ea-form-collection-items"></div>' : '<div class="ea-form-collection-items"><div class="accordion border-0 shadow-none"><div class="form-widget-compound"><div></div></div></div></div>';
            }

            const formTypeNamePlaceholder = addButton.dataset.formTypeNamePlaceholder;
            const labelRegexp = new RegExp(formTypeNamePlaceholder + 'label__', 'g');
            const nameRegexp = new RegExp(formTypeNamePlaceholder, 'g');

            let newItemHtml = addButton.dataset.prototype
                .replace(labelRegexp, numItems)
                .replace(nameRegexp, numItems);

            collection.dataset.numItems = ++numItems;
            const newItemInsertionSelector = isArrayCollection ? '.ea-form-collection-items' : '.ea-form-collection-items .accordion > .form-widget-compound > div';
            const collectionItemsWrapper = collection.querySelector(newItemInsertionSelector);

            EaEditorCollectionProperty.setInnerHTML(collectionItemsWrapper, newItemHtml).then(() => {
                // for complex collections of items, show the newly added item as not collapsed
                if (!isArrayCollection) {
                    EaEditorCollectionProperty.updateCollectionItemCssClasses(collection);
                    EaEditorCollectionProperty.updateCollectionSortable(collection);

                    const collectionItems = collectionItemsWrapper.querySelectorAll('.field-collection-item');
                    const lastElement = collectionItems[collectionItems.length - 1];
                    const lastElementCollapseButton = lastElement.querySelector('.accordion-button');
                    lastElementCollapseButton.classList.remove('collapsed');
                    const lastElementBody = lastElement.querySelector('.accordion-collapse');
                    lastElementBody.classList.add('show');
                }

                document.dispatchEvent(new Event('ea.editor.item-added'));
                document.dispatchEvent(new Event('ea.collection.item-added'));
            })

        });

        //collection.classList.add('processed');
    },

    updateCollectionSortable: (collection) => {
        if (null === collection) {
            return;
        }

        if(collection.querySelector(".ea-form-collection-items .accordion > .form-widget-compound > div")){
            if(collection.sortable){
                collection.sortable.destroy();
                collection.sortable = null;
            }

            collection.sortable = Sortable.create(collection.querySelector(".ea-form-collection-items .accordion > .form-widget-compound > div"),{
                handle: '.field-editor-drag-button',
                direction: 'vertical',
                onEnd: function (evt) {
                    EaEditorCollectionProperty.updateCollectionItemCssClasses(collection);
                },
            });
        }
    },
    updateCollectionItemCssClasses: (collection) => {
        if (null === collection) {
            return;
        }

        const collectionItems = collection.querySelectorAll('.field-collection-item');
        collectionItems.forEach((item, key) => {
            item.querySelectorAll('[name]').forEach((input) => {
                if(input.name.includes("[position]")){
                    input.value = key
                }
            })
        })

        collectionItems.forEach((item) => item.classList.remove('field-collection-item-first', 'field-collection-item-last'));
        const firstElement = collectionItems[0];
        if (undefined === firstElement) {
            return;
        }
        firstElement.classList.add('field-collection-item-first');

        const lastElement = collectionItems[collectionItems.length - 1];
        if (undefined === lastElement) {
            return;
        }
        lastElement.classList.add('field-collection-item-last');

    },
    loadStyle(src) {
        return new Promise(function (resolve, reject) {
            let link = document.createElement('link');
            link.href = src;
            link.rel = 'stylesheet';

            link.onload = () => resolve(link);
            link.onerror = () => reject(new Error(`Style load error for ${src}`));

            document.head.append(link);
        });
    },
    loadScript(src) {
        return new Promise(function (resolve, reject) {
            let script = document.createElement('script');
            script.src = src;
            script.type = "text/javascript"

            script.onload = () => resolve(script);
            script.onerror = () => reject(new Error(`Style load error for ${src}`));

            document.head.append(script);
        });
    },
    setInnerHTML(elm, html) {
        var tmp = document.createElement("div");
        tmp.innerHTML = html;

        let remote = [];
        Array.from(tmp.querySelectorAll("script")).forEach( oldScript => {
            if(oldScript.src){
                remote.push(EaEditorCollectionProperty.loadScript(oldScript.src));
            }
        });

        return Promise.all(remote).then(values => {
            elm.insertAdjacentHTML('beforeend', html);
            Array.from(elm.lastElementChild.querySelectorAll("script")).forEach( oldScript => {
                if(!oldScript.src){
                    const newScript = document.createElement("script");
                    Array.from(oldScript.attributes).forEach( attr => newScript.setAttribute(attr.name, attr.value) );
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                }
            });
        });
    }
};
