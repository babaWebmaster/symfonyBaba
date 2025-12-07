document.addEventListener('DOMContentLoaded', () => {
    const uploadButton = document.querySelector('[data-action-name="uploadMultiple"]');
    

    if (uploadButton) {
        // Crée dynamiquement un input file invisible
        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'multiUpload[]';
        input.accept = 'image/*';
        input.multiple = true;
        input.style.display = 'none';

        uploadButton.parentNode.insertBefore(input,uploadButton);

        uploadButton.addEventListener('click',(e) =>{
            e.preventDefault();
            input.click();
        });

        input.addEventListener('change', () =>{
            const formData = new FormData();

            for(const file of input.files) {
                formData.append('multiUpload[]', file);
            }

             const xhr = new XMLHttpRequest();
             xhr.open('POST', '/admin-baba-5487894613734/back-office/images/bulk-upload', true);

            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                const percent = (event.loaded / event.total) * 100;
               updateProgress(percent);
               
                }
            };

            xhr.onload = () => {
                if (xhr.status === 200) {
                    updateProgress(100);
                alert('Upload terminé');
                window.location.reload();
                } else {
                alert('Erreur lors de l’upload');
                }
            };

            xhr.onerror = () => {
                alert('Erreur réseau');
            };

            xhr.send(formData);
            
                    
        });

      
    }
});


function createProgressBar() {
  // Créer le conteneur
  const container = document.createElement('div');
  container.style.width = '100%';
  container.style.maxWidth = '400px';
  container.style.margin = '20px auto';
  container.style.background = '#eee';
  container.style.borderRadius = '5px';
  container.style.overflow = 'hidden';

  // Créer la barre
  const bar = document.createElement('div');
  bar.id = 'progressBar';
  bar.style.width = '0%';
  bar.style.height = '20px';
  bar.style.background = '#4caf50';
  container.appendChild(bar);

  // Créer le texte
  const text = document.createElement('p');
  text.id = 'progressText';
  text.style.textAlign = 'center';
  text.textContent = '0%';

  // Ajouter au body
  document.body.appendChild(container);
  document.container.appendChild(text);
}

function updateProgress(percent) {
  const bar = document.getElementById('progressBar');
  const text = document.getElementById('progressText');

  if (bar && text) {
    bar.style.width = percent + '%';
    text.textContent = percent.toFixed(0) + '%';
  }
}

