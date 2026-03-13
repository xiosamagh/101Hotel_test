<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Комментарии</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Комментарии</h1>
        
        <!-- Сортировка -->
        <div class="card p-3 mb-3">
            <div class="row">
                <div class="col-md-6">
                    <label>Сортировать по:</label>
                    <select id="sortBy" class="form-control">
                        <option value="id">ID</option>
                        <option value="date">Дате добавления</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Направление:</label>
                    <select id="sortOrder" class="form-control">
                        <option value="desc">По убыванию</option>
                        <option value="asc">По возрастанию</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Список комментариев -->
        <div id="commentsList"></div>
        
        <!-- Modal подтверждения удаления -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтверждение удаления</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите удалить этот комментарий?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Удалить</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Пагинация -->
        <nav class="mt-3">
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
        
        <!-- Форма добавления -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Добавить комментарий</h5>
            </div>
            <div class="card-body">
                <form id="commentForm">
                    <div class="form-group">
                        <label for="name">Email: <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="name" name="name" 
                               placeholder="example@domain.com" required>
                        <small class="form-text text-muted">Введите действительный email адрес</small>
                        <div id="nameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="text">Комментарий: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="text" name="text" rows="4" 
                                  placeholder="Введите ваш комментарий..." required></textarea>
                        <div id="textError"></div>
                    </div>
                    <div class="form-group">
                        <label for="date">Дата: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="date" name="date" 
                               placeholder="Выберите дату..." required readonly>
                        <small class="form-text text-muted">Нажмите для выбора даты</small>
                        <div id="dateError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Отправить</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
    <script>
        let currentPage = 1;
        let sortBy = 'id';
        let sortOrder = 'desc';
        let commentToDelete = null; // ID комментария для удаления

        $(document).ready(function() {
            // Обработчик кнопки подтверждения удаления в modal
            $('#confirmDelete').click(function() {
                if (commentToDelete !== null) {
                    performDelete(commentToDelete);
                    $('#deleteModal').modal('hide');
                    commentToDelete = null;
                }
            });
            flatpickr("#date", {
                locale: "ru",
                dateFormat: "d.m.Y H:i",
                enableTime: true,
                time_24hr: true,
                defaultDate: new Date()
            });
            
            loadComments();
            
            // Обработка изменения сортировки
            $('#sortBy, #sortOrder').change(function() {
                sortBy = $('#sortBy').val();
                sortOrder = $('#sortOrder').val();
                currentPage = 1;
                loadComments();
            });
            
            // Обработка отправки формы
            $('#commentForm').submit(function(e) {
                e.preventDefault();
                clearErrors();
                
                const formData = {
                    name: $('#name').val().trim(),
                    text: $('#text').val().trim(),
                    date: $('#date').val().trim()
                };
                
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                let hasError = false;
                
                if (!formData.name) {
                    $('#nameError').text('Email обязателен для заполнения');
                    $('#name').addClass('is-invalid');
                    hasError = true;
                } else if (!emailPattern.test(formData.name)) {
                    $('#nameError').text('Введите корректный email адрес (например: user@example.com)');
                    $('#name').addClass('is-invalid');
                    hasError = true;
                }
                
                if (!formData.text) {
                    $('#textError').text('Текст комментария обязателен');
                    $('#text').addClass('is-invalid');
                    hasError = true;
                }
                
                if (!formData.date) {
                    $('#dateError').text('Дата обязательна');
                    $('#date').addClass('is-invalid');
                    hasError = true;
                }
                
                if (hasError) return;
                
                $.ajax({
                    url: '<?= base_url('comments/create') ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#commentForm')[0].reset();
                            currentPage = 1;
                            loadComments();
                            
                            const successAlert = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Комментарий успешно добавлен!
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            `;
                            $('.container').prepend(successAlert);
                            setTimeout(() => $('.alert').fadeOut(), 3000);
                        } else {
                            displayErrors(response.errors);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Ошибка при добавлении комментария. Проверьте консоль для деталей.');
                    }
                });
            });
            
            $('#name, #text, #date').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(`#${$(this).attr('id')}Error`).text('');
            });
        });

        function loadComments() {
            $.ajax({
                url: '<?= base_url('comments/get') ?>',
                type: 'GET',
                data: {
                    page: currentPage,
                    sort: sortBy,
                    order: sortOrder
                },
                success: function(response) {
                    if (response.success) {
                        displayComments(response.comments);
                        displayPagination(response.pager);
                    } else {
                        console.error('Error loading comments:', response.message);
                        $('#commentsList').html(
                            '<div class="alert alert-warning">Ошибка загрузки: ' + 
                            (response.message || 'Неизвестная ошибка') + 
                            '</div>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    $('#commentsList').html(
                        '<div class="alert alert-danger">Ошибка при загрузке комментариев. Проверьте консоль.</div>'
                    );
                }
            });
        }

        function displayComments(comments) {
            const container = $('#commentsList');
            container.empty();
            
            if (comments.length === 0) {
                container.html('<div class="alert alert-info">Комментариев пока нет</div>');
                return;
            }
            
            comments.forEach(function(comment) {
                const card = `
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-muted mb-0">
                                    ${escapeHtml(comment.name)} - ${escapeHtml(comment.date)}
                                </h6>
                                <button class="btn btn-sm btn-danger" onclick="deleteComment(${comment.id})">
                                    Удалить
                                </button>
                            </div>
                            <p class="card-text mt-2">${escapeHtml(comment.text)}</p>
                            <small class="text-muted">ID: ${comment.id}</small>
                        </div>
                    </div>
                `;
                container.append(card);
            });
        }

        function displayPagination(pager) {
            const container = $('#pagination');
            container.empty();
            
            if (pager.totalPages <= 1) return;
            
            // Предыдущая
            if (pager.currentPage > 1) {
                container.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${pager.currentPage - 1}); return false;">
                            Предыдущая
                        </a>
                    </li>
                `);
            }
            
            // Страницы
            for (let i = 1; i <= pager.totalPages; i++) {
                const active = i === pager.currentPage ? 'active' : '';
                container.append(`
                    <li class="page-item ${active}">
                        <a class="page-link" href="#" onclick="changePage(${i}); return false;">
                            ${i}
                        </a>
                    </li>
                `);
            }
            
            // Следующая
            if (pager.currentPage < pager.totalPages) {
                container.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${pager.currentPage + 1}); return false;">
                            Следующая
                        </a>
                    </li>
                `);
            }
        }

        function changePage(page) {
            currentPage = page;
            loadComments();
        }

        function deleteComment(id) {
            commentToDelete = id;
            $('#deleteModal').modal('show');
        }
        
        function performDelete(id) {
            $.ajax({
                url: '<?= base_url('comments/delete/') ?>' + id,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        loadComments();
                        
                        const successAlert = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Комментарий успешно удален!
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        `;
                        $('.container').prepend(successAlert);
                        setTimeout(() => $('.alert').fadeOut(), 3000);
                    } else {
                        alert('Ошибка при удалении');
                    }
                },
                error: function() {
                    alert('Ошибка при удалении комментария');
                }
            });
        }

        function displayErrors(errors) {
            for (let field in errors) {
                const errorMsg = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                $(`#${field}Error`).text(errorMsg).addClass('text-danger small mt-1');
                $(`#${field}`).addClass('is-invalid');
            }
        }

        function clearErrors() {
            $('.text-danger').text('').removeClass('text-danger small mt-1');
            $('.form-control').removeClass('is-invalid');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
