// Fonction globale pour gérer le clic sur les leçons
window.handleLessonClick = function(event, url) {
    // Empêcher la propagation de l'événement
    event.preventDefault();
    event.stopPropagation();
    
    // Si le clic vient des boutons d'action, ne pas rediriger
    if (event.target.closest('.lesson-actions')) {
        return;
    }
    
    // Rediriger vers la page de saisie des présences
    window.location.href = url;
};

// Fonction pour ouvrir la modale
function openModal() {
    document.getElementById('editModal').style.display = 'block';
}

// Fonction pour fermer la modale
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Ajouter ces fonctions globales
window.editLesson = function(id) {
    event.preventDefault();
    event.stopPropagation();
    
    fetch(`/coordinators/timetable/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Données reçues:', data); // Debug

        if (!data || !data.id) {
            throw new Error('Données invalides reçues du serveur');
        }

        // Mettre à jour l'action du formulaire
        const form = document.getElementById('edit-form');
        form.action = `/coordinators/timetable/${id}`;

        try {
            // Pré-remplir le formulaire avec les données existantes
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-class-name').value = data.class_id;
            document.getElementById('edit-course-name').value = data.course_id;
            document.getElementById('edit-day').value = data.day_id;
            document.getElementById('edit-time-slot').value = data.time_slot_id;
            document.getElementById('edit-teacher').value = data.teacher_id;
            document.getElementById('edit-color').value = data.color || '#000000';

            // Afficher la modale
            document.getElementById('editModal').style.display = 'block';
        } catch (err) {
            console.error('Erreur lors du remplissage du formulaire:', err);
            throw new Error('Erreur lors du remplissage du formulaire');
        }
    })
    .catch(error => {
        console.error('Error détaillée:', error);
        if (error.message.includes('HTTP error')) {
            alert('Erreur de connexion au serveur. Veuillez réessayer.');
        } else if (error.message.includes('Données invalides')) {
            alert('Les données reçues sont invalides. Veuillez réessayer.');
        } else if (error.message.includes('remplissage du formulaire')) {
            alert('Erreur lors du remplissage du formulaire. Veuillez réessayer.');
        } else {
            alert('Une erreur inattendue est survenue. Veuillez réessayer.');
        }
    });
};

window.deleteLesson = function(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')) {
        fetch(`/coordinators/timetable/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des clics sur les leçons
    document.querySelectorAll('.clickable-lesson').forEach(function(lesson) {
        lesson.addEventListener('click', function(event) {
            console.log('Lesson clicked:', this.dataset.attendanceUrl); // Debug
            if (event.target.closest('.lesson-actions')) {
                console.log('Click on actions, ignoring'); // Debug
                return;
            }
            
            const url = this.dataset.attendanceUrl;
            if (url) {
                console.log('Redirecting to:', url); // Debug
                window.location.href = url;
            }
        });
    });

    // Gestion du formulaire d'ajout
    const addForm = document.getElementById('add-class-form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la création de l\'emploi du temps');
            });
        });
    }

    // Gestion de la suppression
    document.querySelectorAll('.delete-lesson').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')) {
                const id = this.dataset.id;
                fetch(`/coordinators/timetable/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        // Recharger la page après suppression
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    // Gestion de la modale
    const modal = document.getElementById('editModal');
    const closeBtn = document.querySelector('.close-modal');
    
    // Fermer la modale quand on clique sur le X
    closeBtn.onclick = function() {
        closeModal();
    }
    
    // Fermer la modale quand on clique en dehors
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Gestion de la soumission du formulaire d'édition
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editModal').style.display = 'none';
                window.location.reload();
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la mise à jour');
        });
    });

    // Fermer la modale quand on clique sur le X
    document.querySelector('.close-modal').addEventListener('click', function() {
        document.getElementById('editModal').style.display = 'none';
    });

    // Fermer la modale quand on clique en dehors
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}); 