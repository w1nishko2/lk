

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Создание блока</h2>
                <a href="<?php echo e(route('blocks.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад к списку
                </a>
            </div>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('blocks.store')); ?>">
                <?php echo csrf_field(); ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Основная информация</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Название блока <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo e(old('name', $duplicateData['name'] ?? '')); ?>" 
                                               required>
                                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Тип блока <span class="text-danger">*</span></label>
                                        <select class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                id="type" 
                                                name="type" 
                                                required>
                                            <option value="">Выберите тип</option>
                                            <option value="header" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'header' ? 'selected' : ''); ?>>Header (Шапка)</option>
                                            <option value="hero" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'hero' ? 'selected' : ''); ?>>Hero (Главный баннер)</option>
                                            <option value="features" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'features' ? 'selected' : ''); ?>>Features (Возможности)</option>
                                            <option value="services" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'services' ? 'selected' : ''); ?>>Services (Услуги)</option>
                                            <option value="about" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'about' ? 'selected' : ''); ?>>About (О нас)</option>
                                            <option value="contact" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'contact' ? 'selected' : ''); ?>>Contact (Контакты)</option>
                                            <option value="cta" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'cta' ? 'selected' : ''); ?>>CTA (Призыв к действию)</option>
                                            <option value="footer" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'footer' ? 'selected' : ''); ?>>Footer (Подвал)</option>
                                            <option value="content" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'content' ? 'selected' : ''); ?>>Content (Контент)</option>
                                            <option value="gallery" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'gallery' ? 'selected' : ''); ?>>Gallery (Галерея)</option>
                                            <option value="testimonials" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'testimonials' ? 'selected' : ''); ?>>Testimonials (Отзывы)</option>
                                            <option value="pricing" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'pricing' ? 'selected' : ''); ?>>Pricing (Цены)</option>
                                            <option value="team" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'team' ? 'selected' : ''); ?>>Team (Команда)</option>
                                            <option value="blog" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'blog' ? 'selected' : ''); ?>>Blog (Блог)</option>
                                            <option value="other" <?php echo e(old('type', $duplicateData['type'] ?? '') == 'other' ? 'selected' : ''); ?>>Other (Другое)</option>
                                        </select>
                                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-select <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="category_select" 
                                                    name="category_select">
                                                <option value="">Выберите или создайте категорию</option>
                                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($category); ?>" <?php echo e(old('category', $duplicateData['category'] ?? '') == $category ? 'selected' : ''); ?>>
                                                        <?php echo e($category); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <button class="btn btn-outline-secondary" type="button" id="newCategoryBtn">
                                                Новая
                                            </button>
                                        </div>
                                        <input type="text" 
                                               class="form-control mt-2 <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="category" 
                                               name="category" 
                                               value="<?php echo e(old('category', $duplicateData['category'] ?? '')); ?>" 
                                               placeholder="Введите новую категорию"
                                               style="display: none;"
                                               required>
                                        <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="sort_order" class="form-label">Порядок сортировки</label>
                                        <input type="number" 
                                               class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="sort_order" 
                                               name="sort_order" 
                                               value="<?php echo e(old('sort_order', $duplicateData['sort_order'] ?? 0)); ?>" 
                                               min="0">
                                        <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Описание</label>
                                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="description" 
                                              name="description" 
                                              rows="3"><?php echo e(old('description', $duplicateData['description'] ?? '')); ?></textarea>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- HTML контент -->
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">HTML контент <span class="text-danger">*</span></h5>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="previewCode()">
                                    <i class="fas fa-eye"></i> Предпросмотр
                                </button>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control <?php $__errorArgs = ['html_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="html_content" 
                                          name="html_content" 
                                          rows="15" 
                                          required><?php echo e(old('html_content', $duplicateData['html_content'] ?? '')); ?></textarea>
                                <?php $__errorArgs = ['html_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    Введите HTML код блока. Используйте Bootstrap классы для стилизации.
                                </small>
                            </div>
                        </div>

                        <!-- CSS контент -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">CSS стили</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control <?php $__errorArgs = ['css_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="css_content" 
                                          name="css_content" 
                                          rows="10"><?php echo e(old('css_content', $duplicateData['css_content'] ?? '')); ?></textarea>
                                <?php $__errorArgs = ['css_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    Дополнительные CSS стили для блока (необязательно).
                                </small>
                            </div>
                        </div>

                        <!-- JavaScript контент -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">JavaScript код</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control <?php $__errorArgs = ['js_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="js_content" 
                                          name="js_content" 
                                          rows="8"><?php echo e(old('js_content', $duplicateData['js_content'] ?? '')); ?></textarea>
                                <?php $__errorArgs = ['js_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    JavaScript код для блока (необязательно).
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card sticky-top">
                            <div class="card-header">
                                <h5 class="mb-0">Настройки</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           <?php echo e(old('is_active', $duplicateData['is_active'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="is_active">
                                        Активен
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Активные блоки доступны для использования в конструкторе.
                                </small>
                            </div>
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Создать блок
                                    </button>
                                    <a href="<?php echo e(route('blocks.index')); ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Отмена
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно предпросмотра -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Предпросмотр блока</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_select');
    const categoryInput = document.getElementById('category');
    const newCategoryBtn = document.getElementById('newCategoryBtn');

    // Переключение между выбором существующей и созданием новой категории
    newCategoryBtn.addEventListener('click', function() {
        if (categoryInput.style.display === 'none') {
            categoryInput.style.display = 'block';
            categorySelect.style.display = 'none';
            categoryInput.required = true;
            categorySelect.required = false;
            newCategoryBtn.textContent = 'Выбрать';
        } else {
            categoryInput.style.display = 'none';
            categorySelect.style.display = 'block';
            categoryInput.required = false;
            categorySelect.required = true;
            newCategoryBtn.textContent = 'Новая';
        }
    });

    // Синхронизация значений
    categorySelect.addEventListener('change', function() {
        if (this.value) {
            categoryInput.value = this.value;
        }
    });

    categoryInput.addEventListener('input', function() {
        categorySelect.value = '';
    });
});

function previewCode() {
    const htmlContent = document.getElementById('html_content').value;
    const cssContent = document.getElementById('css_content').value;
    const jsContent = document.getElementById('js_content').value;

    if (!htmlContent.trim()) {
        alert('Введите HTML контент для предпросмотра');
        return;
    }

    const previewHtml = `
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Предпросмотр блока</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            ${cssContent}
        </style>
    </head>
    <body>
        ${htmlContent}
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        ${jsContent ? `<script>${jsContent}</script>` : ''}
    </body>
    </html>`;

    const previewFrame = document.getElementById('previewFrame');
    const blob = new Blob([previewHtml], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    previewFrame.src = url;

    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\konstructor\resources\views\blocks\create.blade.php ENDPATH**/ ?>