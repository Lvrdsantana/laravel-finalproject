// Gestionnaire d'événements pour la recherche d'utilisateurs
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const tableBody = document.querySelector('.user-list tbody');
    const paginationWrapper = document.querySelector('.pagination-wrapper');
    
    if (!searchInput) return;

    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchValue = this.value.trim();
            
            // Afficher l'indicateur de chargement
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `;

            // Requête AJAX pour la recherche
            fetch(`/users/search?search=${encodeURIComponent(searchValue)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                let html = '';
                
                if (data.users.data.length === 0) {
                    html = `
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No users found
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    data.users.data.forEach(user => {
                        html += `
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            ${user.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <div class="user-name">${user.name}</div>
                                            <small class="text-muted">ID: #${user.id}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>${user.email}</td>
                                <td>
                                    <span class="role-badge role-${user.role}">
                                        ${user.role}
                                    </span>
                                </td>
                                <td class="user-actions">
                                    <button class="btn-action btn-edit" data-toggle="modal" 
                                            data-target="#editUserModal" 
                                            data-id="${user.id}" 
                                            data-name="${user.name}" 
                                            data-email="${user.email}" 
                                            data-role="${user.role}"
                                            data-class-id="${user.role === 'students' && user.student ? user.student.class_id : ''}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="/users/${user.id}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                tableBody.innerHTML = html;
                
                // Mise à jour de la pagination
                if (data.pagination) {
                    paginationWrapper.innerHTML = data.pagination;
                }
                
                // Mise à jour des informations de pagination
                const paginationInfo = document.querySelector('.pagination-info');
                if (paginationInfo) {
                    paginationInfo.textContent = `Showing ${data.users.from || 0} to ${data.users.to || 0} of ${data.users.total} users`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                An error occurred while searching
                            </div>
                        </td>
                    </tr>
                `;
            });
        }, 300);
    });

    // Style de la barre de recherche
    searchInput.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
    });

    searchInput.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
    });
});

// Gestion des sélecteurs en fonction du rôle
$('select[name="role"]').change(function() {
    if ($(this).val() === 'students') {
        $('#classSelect').show();
        $('#studentSelect').hide();
    } else if ($(this).val() === 'parents') {
        $('#classSelect').hide();
        $('#studentSelect').show();
    } else {
        $('#classSelect').hide();
        $('#studentSelect').hide();
    }
});

// Gestion des sélecteurs dans le modal d'édition
$('#editRole').change(function() {
    if ($(this).val() === 'students') {
        $('#editClassDiv').show();
        $('#editStudentDiv').hide();
        $('#editClassSelect').attr('required', 'required');
        $('#editStudentSelect').removeAttr('required');
    } else if ($(this).val() === 'parents') {
        $('#editClassDiv').hide();
        $('#editStudentDiv').show();
        $('#editClassSelect').removeAttr('required');
        $('#editStudentSelect').attr('required', 'required');
    } else {
        $('#editClassDiv').hide();
        $('#editStudentDiv').hide();
        $('#editClassSelect').removeAttr('required');
        $('#editStudentSelect').removeAttr('required');
    }
});

// Initialisation du modal d'édition
$(document).ready(function () {
    // Gestion de l'affichage des champs en fonction du rôle
    $('#editRole').change(function () {
        if ($(this).val() === 'students') {
            $('#editClassDiv').show();
            $('#editStudentDiv').hide();
            $('#editClassSelect').attr('required', 'required');
            $('#editStudentSelect').removeAttr('required');
        } else if ($(this).val() === 'parents') {
            $('#editClassDiv').hide();
            $('#editStudentDiv').show();
            $('#editClassSelect').removeAttr('required');
            $('#editStudentSelect').attr('required', 'required');
        } else {
            $('#editClassDiv').hide();
            $('#editStudentDiv').hide();
            $('#editClassSelect').removeAttr('required');
            $('#editStudentSelect').removeAttr('required');
        }
    });

    // Pré-remplissage du formulaire d'édition
    $('#editUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('id');
        var userName = button.data('name');
        var userEmail = button.data('email');
        var userRole = button.data('role');
        var userClassId = button.data('class-id');
        var studentIds = button.data('student-ids');

        var modal = $(this);
        modal.find('#editUserId').val(userId);
        
        // Séparation du nom complet
        var nameParts = userName.split(' ');
        modal.find('#editFirstName').val(nameParts[0]);
        modal.find('#editLastName').val(nameParts.slice(1).join(' '));
        
        modal.find('#editEmail').val(userEmail);
        modal.find('#editRole').val(userRole);

        // Gestion de l'affichage des champs selon le rôle
        if (userRole === 'students') {
            $('#editClassDiv').show();
            $('#editStudentDiv').hide();
            $('#editClassSelect').val(userClassId);
        } else if (userRole === 'parents') {
            $('#editClassDiv').hide();
            $('#editStudentDiv').show();
            if (studentIds) {
                $('#editStudentSelect').val(studentIds.split(','));
            }
        } else {
            $('#editClassDiv').hide();
            $('#editStudentDiv').hide();
        }

        // Configuration de l'action du formulaire
        var formAction = "{{ url('users') }}/" + userId;
        $('#editUserForm').attr('action', formAction);
    });
});