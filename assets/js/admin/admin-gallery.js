

  function openGallery(){
   fetch('/admin-baba-5487894613734/back-office/api/images')
  .then(res => res.json())
  .then(images => {
     const existingGallery = document.querySelector('.container-gallery');
     const existingGalleryMaquette = document.querySelector('.container-image'); 
     const hiddenValue = document.querySelector('input[name="Maquette[imagePreview]"]');

     if(! existingGallery){
         if(! existingGalleryMaquette){
                if(hiddenValue.value != null){
                    hiddenValue.value = null;
                }
            }

        addGallery(images);

        if(! existingGalleryMaquette){
                addGalleryInput('.image-selector-input');           
           
        }
        
     }
          
  });
  }
  
  function addImageEdit(){
    const hiddenValue = document.querySelector('input[name="Maquette[imagePreview]"]').value;

    const existingGalleryMaquette = document.querySelector('.container-image'); 

      if(hiddenValue != null && !existingGalleryMaquette){
        sendIdsFetch(hiddenValue);
      }
   
  }

  function sendIdsFetch(value){
    fetch('/admin-baba-5487894613734/back-office/api/gallery_images',{
        method: 'POST',
        headers: {
            'Content-Type': 'text/plain',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body : value})
        .then(response => response.json())
        .then(data => {
             addGalleryInput('.image-selector-input');

             data.forEach(item =>{
                addImageGallery(item.url,item.id,'.container-image');
                setupImageInteractions('.container-image');
             })

             
        })
        .catch(error => console.error('Erreur fetch :', error));

  }




  //function pour sélectionner les images dans la galerie de la médiathèque
  function selectImage(selector){
    document.querySelectorAll('.image-item').forEach(item => {
        
            item.addEventListener('click', () => {
                if(item.dataset.selectable === 'true'){
                    item.classList.toggle('selected');
                    item.classList.add('disabled');
                    item.dataset.selectable = 'false';
                    changeHidden(selectIds(selector));
                    removeImageGallery(item.dataset.id);             
                   
                }
                else{
                    item.classList.toggle('disabled');
                    item.classList.add('selected');
                    item.dataset.selectable = 'true';
                    addImageGallery(item.lastElementChild.lastChild.src,item.dataset.id,'.container-image');
                    changeHidden(selectIds(selector));
                }
                
            })
    })
  }

  //function pour ajouter les datas aux images qui sont déjà sélectionnées dans le form 
function addClassDataOpenGallery(selector) {
  const hiddenValue = document.querySelector(selector)?.value;
  if (hiddenValue != null && hiddenValue.trim() !== '') {
    const array = hiddenValue.split(',').map(id => id.trim());

    array.forEach(id => {
      const image = document.querySelector(`.container-gallery .image-item[data-id="${id}"]`);
      if (image) {
        image.classList.remove('disabled');
        image.classList.add('selected');
        image.dataset.selectable = 'true';
      }
    });
  }
}

//function pour ajouter la galerie des images dans le form 
function addGallery(images){

     const galleryHtml = generateGalleryHtml(images);
     const body = document.querySelector('body');
     const wrapper = document.createElement('div');
     wrapper.classList.add('container-gallery','d-flex','flex-row','flex-wrap','position-fixed','align-items-baseline');
     wrapper.innerHTML = galleryHtml;
     wrapper.appendChild(addCloseButton());
     body.appendChild(wrapper);
     addClassDataOpenGallery('input[name="Maquette[imagePreview]"]');
     selectImage('[data-selectable="true"]');
     closeGallery();

}

//function afin de récupérer les ids de l'ensemble des images sélectionnées. 
function selectIds(selector){
    const elementsSelectable =document.querySelectorAll(selector);
    const selectIds=[];
     
      elementsSelectable.forEach(item => {
        selectIds.push(item.dataset.id);
      })

    return result = selectIds.join(',');
}

//function afin de mettre à jour la valeur de l'input hidden
function changeHidden(value){
    const hiddenInput = document.querySelector('input[name="Maquette[imagePreview]"]');
    hiddenInput.value = value ;
}

//function pour ajouter le button close aux images du form
function addCloseButton() {
  const button = document.createElement('button');
  button.className = 'close-gallery';
  button.textContent = '✖ Fermer';
  
  return button;
}


//function pour générer le bloc image dans la galerie de la médiathèque
function generateGalleryHtml(images){
    
    let gallery='' ;

    images.forEach(image =>{
        gallery += `
    <div class="image-item disabled pe-1 ps-1" data-id="${image.id}" data-selectable="false">
      <a> <img 
        src="${image.url}" 
      ></a>
     </div>`
    });

    return gallery ;
}

//function pour fermer la médiathèque
function closeGallery(){

    document.addEventListener('click', event => {
    if (event.target.classList.contains('close-gallery')) {
    const wrapper = event.target.closest('.container-gallery');
    if (wrapper) wrapper.remove(); //  supprime la galerie du DOM
    }
    });

}

//function pour créer le container des images du form
function addGalleryInput(selector){
    const input = document.querySelector(selector);
    const containerInput = input.parentElement;
    const addGalleryMaquette = document.createElement('div');
    addGalleryMaquette.classList.add('container-image','d-flex','flex-row');
    containerInput.appendChild(addGalleryMaquette);
    
    setupDragAndDrop('.container-image');
   
    }

//function pour créer les images sélectionnées dans la médiathèque pour les injecter dans le form. 
function addImageGallery(src,id,container){
    const imageWrapper = document.createElement('div');
    const image = document.createElement('img');
    const buttonClose = document.createElement('button');
    buttonClose.classList.add('delete-btn');
    buttonClose.innerHTML = 'x';
    imageWrapper.appendChild(buttonClose);
    imageWrapper.appendChild(image);
    imageWrapper.classList.add('image-item','pe-1','ps-1');
    imageWrapper.dataset.id = id;
    imageWrapper.dataset.deleted = 'false';
    const containerImage = document.querySelector(container);
    image.src = src;
    imageWrapper.setAttribute('draggable','true');
    containerImage.appendChild(imageWrapper);
}

//function supprime l'image de la galerie du form quand elle est désélectionner de la médiathèque.
function removeImageGallery(id) {
  const image = document.querySelector('.container-image .image-item[data-id="'+ id + '"]');
  
  if (image) {
    image.remove(); // ou image.classList.add('fade-out') + setTimeout(...)
  }
  
  
}

//function pour supprimer l'image du form
function setupImageInteractions(selector) {
  const container = document.querySelector(selector);
  if (!container) return;

  container.addEventListener('click', (event) => {
    const deleteBtn = event.target.closest('.delete-btn');
    if (deleteBtn) {
        event.preventDefault();
      const imageItem = deleteBtn.closest('.image-item');
      if (imageItem) {
        imageItem.remove();
        changeHidden(selectIds('.container-image .image-item'));
        console.log(`Image supprimée : ${imageItem.dataset.id}`);
      }
      return; //stop ici, ne pas continuer vers toggle
    }

    const imageItem = event.target.closest('.image-item');
    if (imageItem) {
      imageItem.classList.toggle('selected');
    }
  });
}




document.addEventListener('DOMContentLoaded',() => {
const addButton = document.createElement('a');
const container = document.querySelector('.image-selector-label');
const parent = container.parentElement;
addButton.href = '#';
addButton.textContent = 'Ajouter une image';
addButton.classList.add('btn', 'btn-primary');

parent.appendChild(addButton);

addButton.addEventListener('click',(e) =>{
    e.preventDefault();
    openGallery();
})

  addImageEdit();

})

document.addEventListener('DOMContentLoaded', () => {
  setupImageInteractions('#new-Maquette-form');
  
});

function setupDragAndDrop(containerSelector){
     let dragged = null;
     let offsetX = 0;
     let offsetY = 0;
     let offXCenterImg = 0;
     let offYcenterImg = 0;
       const dropFeedback = document.createElement('div');
             dropFeedback.classList.add('dropIndicator','ps-1','pe-1');
             dropFeedback.style.width = '150px';
             dropFeedback.style.height = '150px';
       

     const container = document.querySelector(containerSelector);
         if(!container) return;

         //dragSTART
         container.addEventListener('dragstart',(e) =>{
                dragged = e.target.closest('.image-item');
                const rect = dragged.getBoundingClientRect();
                dropFeedback.style.width = rect.width;
                dropFeedback.style.height = rect.height; 
                
                offsetX = e.clientX;
                offsetY = e.clientY;
                offXCenterImg =rect.left + (rect.width/2);
                offYcenterImg = rect.top + (rect.height/2);
                

         });

         //dragOVER
         container.addEventListener('dragover', (e) => {
           
            
             e.preventDefault(); // indispensable pour que le drop fonctionne
             const targetItem = e.target.closest('.image-item');   
              
                      
             
              const offxCenterDragOver = calculCenter(offsetX,e.clientX);

              
             const result = getDragAfterElement(container,offxCenterDragOver);
             const afterElement = result?.element;              
             
            if(dragged != targetItem)
                {dragged.classList.add('dragging');
                    if (afterElement && container.contains(afterElement)) {
                     container.insertBefore(dropFeedback, afterElement);
                    } else {
                    container.appendChild(dropFeedback);
                     }

                }
            


             
        });

        //DragEND
        container.addEventListener('dragend', (e) => {
                         
                dragged.closest('.image-item').classList.remove('dragging');
                const ids = selectIds(containerSelector + ' .image-item');
                changeHidden(ids);

        });


        //DROP
         container.addEventListener('drop', (e) =>{
            dropFeedback.remove();
            dragged.classList.remove('dragging');
             e.preventDefault();
                              
          
             const offXCenterDrag = calculCenter(offsetX,e.clientX);
                             
        const result = getDragAfterElement(container,offXCenterDrag);
        const afterElement = result?.element;

        if (afterElement && container.contains(afterElement)) {
              container.insertBefore(dragged.closest('.image-item'), afterElement)
         } else {
         container.appendChild(dragged.closest('.image-item'));
         }

          
         });

}




function calculCenter(offsetX,x){
    const shift = offsetX - x;
    const center = offsetX - shift;
    return center;
}

function getDragAfterElement(container, x){
     const items = [...container.querySelectorAll('.image-item:not(.dragging)')];
        
    return items.reduce((closest, child) => {
           const box = child.getBoundingClientRect();
           const offset = x - (box.left+(box.width/2));
           if(offset < 0 && offset > closest.offset){
            return { offset, element: child };
           }else{
            return closest;
           } 

    },{offset: Number.NEGATIVE_INFINITY, element: null});
}







